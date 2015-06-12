<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;

use GoSession;

use Register\Entity\OrderTable as OrderTableEntity;

class OrderTableController extends AbstractActionController
{
    const STATUS_ENTITY         = 'Data\Entity\Status';
    const STORE_ENTITY          = 'Data\Entity\Store';
    const USER_ENTITY           = 'User\Entity\User';
    const ORDER_ENTITY          = 'Register\Entity\Order';
    const ORDER_TABLE_ENTITY    = 'Register\Entity\OrderTable';
    const PRODUCT_ENTITY        = 'Product\Entity\Product';
    const PRODUCT_QTY_ENTITY    = 'Product\Entity\ProductCurrentQty';
    const BRAND_ENTITY          = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY       = 'Catalog\Entity\Catalog';

    /**
     * @var
     */
    protected $em;
    private $cache;
    private $fullNameService;

    public function indexAction()
    {
        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();

        $currentSession = new Container();

        //Сохранение и получение текущего id записи из таблицы Register
        if (isset($currentSession->idOrder)) {
            $idOrder = $currentSession->idOrder;
        } else {
            $currentSession->idOrder = $this->params('content');
            $idOrder = $currentSession->idOrder;
        }

        $qs = null;

        $filter = array();

        // в этом кэше хранятся брэнд и/или категория товаров, выбранные пользователем
        $filterCache = unserialize($this->cache->getItem($currentUser));

        // если этого кэша нет, создаем его (с пустым массивом)
        if (!$filterCache) {
            $filterCache = $filter;
            $this->cache->addItem($currentUser, serialize($filterCache));
        } else {
            $filter = $filterCache;
        }

        $request = $this->getRequest();
        // здесь записываем в $filter выбранные пользователем брэнд и/или категорию
        if ($request->isPost()) {
            if ($request->getPost('idBrand') || $request->getPost('idCatalog')) {
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qs = $qb->select('p')->from(self::PRODUCT_ENTITY, 'p');

                if ($request->getPost('idBrand')) {
                    $filter['idBrand'] = $request->getPost('idBrand');
                    $qb->andWhere('p.idBrand = :idBrand')->setParameter('idBrand', $filter['idBrand']);
                }

                if ($request->getPost('idCatalog')) {
                    $filter['idCatalog'] = $request->getPost('idCatalog');
                    $qb->andWhere('p.idCatalog = :idCatalog')->setParameter('idCatalog', $filter['idCatalog']);
                }
            } else {
                $this->cache->removeItem($currentUser);
            }
        }

        $this->cache->replaceItem($currentUser, serialize($filter));

        $product = !is_null($qs) ? $qs->getQuery()->getResult() : $qs;

        if (!$idOrder) {
            return $this->redirect()->toRoute('order');
        }

        $order = $this->getEntityManager()->find(self::ORDER_ENTITY, $idOrder);

        $catalogList = $this->getCatalogList();

        $idProduct = $this->getRequest()->getPost('idProduct');

        if ($idProduct) {
            $currentProductArr = array();

            $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);

            $currentProductArr['id']    = $currentProduct->getId();
            $currentProductArr['name']  = $currentProduct->getName();

            // Добавление введенных к-ва и цены в свойства выбранного товара
            $currentProductArr['qty']   = (int)$this->getRequest()->getPost('qty');
            $currentProductArr['price'] = $this->getRequest()->getPost('price');

            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $currentProduct->getIdBrand());
            $currentProductArr['brand'] = $brand->getName();

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $currentProduct->getIdCatalog());
            $currentProductArr['category'] = $this->fullNameService->getFullNameCategory($category->getId());
        }

        // Формирование массива с выбранными товарами
        if (isset($currentSession->productList) && $currentProductArr) {
            $currentSession->productList[] = $currentProductArr;
        } elseif ($currentProductArr) {
            $currentSession->productList = array();
            $currentSession->productList[] = $currentProductArr;
        }

        //Добавление свойства с текущим количеством товара
        if ($product->result) {
            for ($i = 0, $count = count($product->result); $i < $count; $i++) {
                $idProduct = $product->result[$i]->getId();
                $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);

                $qtyProduct = $entityQtyProduct->getQty();
                $product->result[$i]->setQty($qtyProduct);
            }
        }

        return new ViewModel(array(
            'order'       => $order,
            'catalogList' => $catalogList,
            'product'     => $product,
            'productList' => $currentSession->productList,
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function addAction()
    {
        $currentSession = new Container();

        $order = $this->getEntityManager()->find(self::ORDER_ENTITY, $currentSession->idOrder);

        $orderTable = array();

        $productList = $currentSession->productList;

        $totalSum = 0;

        for ($i = 0, $count = count($productList); $i < $count; $i++) {
            $idProduct  = $productList[$i]['id'];
            $product    = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);
            $qty        = $productList[$i]['qty'];
            $price      = $productList[$i]['price'] * 100;
            $totalSum  += $qty * $price;

            unset($productList[$i]['qty']);
            unset($productList[$i]['price']);

            $idUser = $order->getIdUser();
            $user   = $this->getEntityManager()->find(self::USER_ENTITY, $idUser);

            unset($productList[$i]['brand']);
            unset($productList[$i]['category']);

            $noteOrderTable = array(
                'idProduct'   => $product,
                'qty'         => $qty,
                'price'       => $price,
                'idOrder'     => $order,
                'idUser'      => $user
            );
            $orderTable[] = $noteOrderTable;
        }

        foreach ($orderTable as $item) {
            $orderTableNote = new OrderTableEntity();

            $orderTableNote->populate($item);

            $this->getEntityManager()->persist($orderTableNote);
            $this->getEntityManager()->flush();
        }

        $order->setTotalSum($totalSum);

        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();

        unset($currentSession->idBrand);
        unset($currentSession->idCatalog);
        unset($currentSession->idOrder);
        unset($currentSession->productList);

        return $this->redirect()->toRoute('order');
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function getDetailAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $idOrder = $this->getRequest()->getPost('idOrder');
            $order = $this->getEntityManager()->find(self::ORDER_ENTITY, $idOrder);

            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('ot')
                ->from(self::ORDER_TABLE_ENTITY ,'ot')
                ->where('ot.idOrder = :idOrder')
                ->setParameter('idOrder', $order->getId());

            $orderTable = $qb->getQuery()->getResult();

            return new ViewModel(array(
                'order'      => $order,
                'orderTable' => $orderTable
            ));
        }

        return $this->redirect()->toRoute('order');
    }

    /**
     * @return \Zend\Http\Response
     */
    public function clearAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $currentSession = new Container();

            $orderTable = $this->getEntityManager()->getRepository(self::ORDER_TABLE_ENTITY)->findBy(array('idOrder' => $currentSession->idOrder));

            if (empty($orderTable)) {
                $order = $this->getEntityManager()->getRepository(self::ORDER_ENTITY)->findBy(array('id' => $currentSession->idOrder));

                foreach ($order as $orderItem) {
                    $this->getEntityManager()->remove($orderItem);
                }

                $this->getEntityManager()->flush();
            }
        }

        return $this->redirect()->toRoute('order');
    }

    /**
     * Set cache from factory
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Set fullName service
     *
     * @param $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullNameService = $fullName;
    }

    /**
     * @return array|mixed
     */
    private function getCatalogList()
    {
        $result = array();

        $this->cache->removeItem('catalogList');

        if (!$this->cache->hasItem('catalogList')) {
            $brand    = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)->findAll();
            $categories = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)->findAll();

            foreach ($categories as $categoryItem) {
                if (!is_null($categoryItem->getIdParent())) {
                    $mainCategory[] = $categoryItem->getIdParent()->getId();
                }
            }

            $mainCategory = array_unique($mainCategory);

            $result['category'] = array();
            $result['category'][0]['id'] = null;
            $result['category'][0]['name'] = 'Выбрать категорию';

            $result['brand'] = array();
            $result['brand'][0]['id'] = null;
            $result['brand'][0]['name'] = 'Выбрать производителя';

            foreach ($categories as $categoryItem) {
                if (!in_array($categoryItem->getId(), $mainCategory)) {
                    // Для того чтобы выставить ключи массива по-порядку
                    // потому как array_unique сохраняет значение ключей
                    // используем array_push
                    array_push(
                        $result['category'],
                        array(
                            'id'   => $categoryItem->getId(),
                            'name' => $this->fullNameService->getFullNameCategory($categoryItem->getId()),
                        )
                    );
                }

                $this->fullNameService->setFullNameToNull();
            }

            $count = 1;
            foreach ($brand as $brandItem) {
                $result['brand'][$count]['id'] = $brandItem->getId();
                $result['brand'][$count]['name'] = $brandItem->getName();
                ++$count;
            }

            $this->cache->setItem('catalogList', serialize($result));
        } else {
            $result = unserialize($this->cache->getItem('catalogList'));
        }

        return $result;
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
