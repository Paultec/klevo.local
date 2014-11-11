<?php

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\DateTime;

use Cart\Entity\CartEntity;
use Cart\Entity\CartTable;

use GoSession;

class CartController extends AbstractActionController
{
    const CART_ENTITY           = 'Cart\Entity\CartEntity';
    const CART_TABLE            = 'Cart\Entity\CartTable';
    const PRODUCT_ENTITY        = 'Product\Entity\Product';
    const PRODUCT_ENTITY_QTY    = 'Product\Entity\ProductCurrentQty';
    const DELIVERY_METHOD       = 'Data\Entity\DeliveryMethod';
    const PAYMENT_METHOD        = 'Data\Entity\PaymentMethod';

    /**
     * @var
     */
    protected $em;
    private $currentSession;

    public function __construct()
    {
        $this->currentSession = new Container();
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = (array)$request->getPost();

            // удаление товара(ов) из корзины
            if (isset($postData['removeAll'])) {
                // удалить все товары из корзины
                $this->removeAll();

                return $this->redirect()->toRoute('home');
            } elseif (isset($postData['removeItem'])) {
                $this->removeItem($postData);

                return $this->redirect()->toRoute('cart');
            }

            // если это покупка в 1 клик
            if (isset($postData['oneClickBuy'])) {
                // убрать данные о способе доставки и способе оплаты
                unset($postData['delivery']);
                unset($postData['payment']);
            }

            // проверить post параметры
            $checkResult = $this->checkPostData($postData);

            if (!$checkResult) {
                var_dump('error');
            }

            // выбираем продукты из корзины
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qs =  $qb
                ->select(array('p', 'q.qty as quantity'))
                ->from(self::PRODUCT_ENTITY, 'p')
                ->join(
                    self::PRODUCT_ENTITY_QTY, 'q',
                    'WITH', 'p.id = q.idProduct'
                )
                ->where(
                    $qb->expr()->in('p.id', $postData['id'])
                )
                ->getQuery();

            try {
                $qr = $qs->getArrayResult();
            } catch (\Exception $e) {
                var_dump('error');
            }

            $result = array();

            $selectedQty = $postData['qty']; // к-во выбранных пользователем товаров

            for ($i = 0, $count = count($selectedQty); $i < $count; $i++) {
                if ((int)$selectedQty[$i] > $qr[$i]['quantity']) {
                    var_dump('error'); // выбрано много товаров
                } else {
                    $result[$i]             = $qr[$i][0];
                    $result[$i]['quantity'] = (int)$selectedQty[$i];
                }
            }

            // данные пользователя
            $user = $this->forward()->dispatch('Data/Controller/CartUserHelp',
                array('action' => 'index', 'postData' => $postData))->getVariables();

            // заменить idUser к корзине
            // если зарегистрированный пользователь уже покупал,
            // как не зарегистрированный
            if (!empty($user['changeIdUserInCart'])) {
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::CART_ENTITY, 'c')
                    ->set('c.idUser', '?1')
                    ->where('c.idUser = ?2')
                    ->setParameter(1, $user['changeIdUserInCart']['to'])
                    ->setParameter(2, $user['changeIdUserInCart']['from'])
                    ->getQuery();
                $qu->execute();

                /*
                 * @todo придумать другое решение
                 */
                $this->forward()->dispatch('Data/Controller/CartUserHelp',
                    array('action' => 'remove-tmp-user', 'postData' => $user['changeIdUserInCart']['from']));
            }

            // запись данных в Cart Table
            $cartEntity = new CartEntity();

            // способ оплаты и доставки (при покупке через корзину)
            $currentDeliveryMethod = $this->getEntityManager()->getRepository(self::DELIVERY_METHOD)->findOneBy(array('id' => (int)$postData['delivery']));
            $currentPaymentMethod  = $this->getEntityManager()->getRepository(self::PAYMENT_METHOD)->findOneBy(array('id' => (int)$postData['payment']));

            $cartEntity->setDate(new \DateTime());
            $cartEntity->setIdUser($user['user']);
            $cartEntity->setDeliveryMethod($currentDeliveryMethod);
            $cartEntity->setPaymentMethod($currentPaymentMethod);

            $this->getEntityManager()->persist($cartEntity);

            // запись данных в Cart Entity
            $prepareData = array();

            foreach ($result as $product) {
                $cartTable  = new CartTable();

                $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, (int)$product['id']);

                $prepareData['idCartEntity'] = $cartEntity;
                $prepareData['idProduct']    = $currentProduct;
                $prepareData['qty']          = $product['quantity'];
                $prepareData['price']        = $product['price'];

                $cartTable->populate($prepareData);

                $this->getEntityManager()->persist($cartTable);
            }

            // Подсчитать общую сумму заказа
            foreach ($result as $item) {
                $result['total'] += $item['price'] * $item['quantity'];
            }

            // новая общая сумма покупки
            $totalBuy = (int)$user['userAddition']->getTotalBuy() + $result['total'];
            $user['userAddition']->setTotalBuy($totalBuy);

            $this->getEntityManager()->flush();

            // очищаем корзину
            $this->removeAll();

            $this->currentSession->success = $result;

            return $this->prg('/cart/success', true);
        }

        // получить товары из корзины
        $cart = $this->getCartProducts($this->currentSession->cart);

        // получить доступные способы оплаты и доставки
        $delivery   = $this->getEntityManager()->getRepository(self::DELIVERY_METHOD)->findAll();
        $payment    = $this->getEntityManager()->getRepository(self::PAYMENT_METHOD)->findAll();

        // данные пользователя
        $userInfo = $this->forward()->dispatch('Data/Controller/CartUserHelp',
            array('action' => 'user'))->getVariables();

        $viewModel = new ViewModel(array(
                'cart'      => $cart,
                'delivery'  => $delivery,
                'payment'   => $payment,
                'userInfo'  => $userInfo,
                'continue'  => $this->currentSession->continue
            ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));
        $viewModel->addChild($catalog, 'catalog');

        return $viewModel;
    }

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost();

            if (empty($postData['id'])) {
                var_dump('error');
            }

            if (!isset($this->currentSession->cart)) {
                $this->currentSession->cart = array();
                $this->currentSession->cart[] = (int)$postData['id'];
            } else {
                // Если товара еще нет в корзине
                if (!in_array((int)$postData['id'], $this->currentSession->cart)) {
                    $this->currentSession->cart[] = (int)$postData['id'];
                }
            }

            // от куда добавил в корзину
            $this->currentSession->continue = $postData['continue'];

            return $this->prg('/cart', true);
        }

        return $this->redirect()->toRoute('cart');
    }

    public function successAction()
    {
        if (isset($this->currentSession->success)) {
            // Товары, которые заказал пользователь
            $result = $this->currentSession->success;

            unset($this->currentSession->success);

            $viewModel = new ViewModel(array(
                    'result' => $result
                ));

            $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));
            $viewModel->addChild($catalog, 'catalog');

            return $viewModel;
        }

        return $this->redirect()->toRoute('home');
    }

    protected function checkPostData(array $postData)
    {
        $flag = true;

        if (strlen($postData['phone']) != 9) {
            $flag = !$flag;
        }

        foreach ($postData['id'] as $postId) {
            if (empty((int)$postId)) {
                $flag = !$flag;
            }
        }

        foreach ($postData['qty'] as $postQty) {
            if ($postQty <= 0) {
                $flag = !$flag;
            }
        }

        return $flag;
    }

    protected function removeAll()
    {
        if (isset($this->currentSession->cart)) {
            unset($this->currentSession->cart);
        }
    }

    protected function removeItem($postData)
    {
        $key = array_search((int)$postData['removeItem'], $this->currentSession->cart);

        if ($key !== false) {
            unset($this->currentSession->cart[$key]);
        }
    }

    protected function getCartProducts($cartSession)
    {
        if (empty($cartSession)) { return false; }

        // Выбор товаров из корзины
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs = $qb
            ->select(array('p', 'q.qty as quantity'))
            ->from(self::PRODUCT_ENTITY, 'p')
            ->join(
                self::PRODUCT_ENTITY_QTY, 'q',
                'WITH', 'p.id = q.idProduct'
            )
            ->where(
                $qb->expr()->in('p.id', $cartSession)
            )
            ->getQuery();

        return $qs->getArrayResult();
    }

    /**
     * @return array|object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }
}