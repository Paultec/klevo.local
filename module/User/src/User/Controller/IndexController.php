<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class IndexController extends AbstractActionController
{
    const USER_ENTITY           = 'User\Entity\User';
    const USER_ADDITION_ENTITY  = 'User\Entity\UserAddition';
    const DELIVERY_METHOD       = 'Data\Entity\DeliveryMethod';
    const PAYMENT_METHOD        = 'Data\Entity\PaymentMethod';
    const CART_ENTITY           = 'Cart\Entity\CartEntity';
    const PRODUCT_ENTITY        = 'Product\Entity\Product';
    const REGISTER_ENTITY       = 'Register\Entity\Register';
    const REGISTER_TABLE_ENTITY = 'Register\Entity\RegisterTable';

    protected $em;
    private $currentUser;

    public function __construct()
    {
        $auth = new AuthenticationService();
        $this->currentUser = $auth->getIdentity();
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $purchasedProducts = $this->getPurchasedProducts(null);

            return new JsonModel(array(
                'purchasedProducts' => $purchasedProducts
            ));
        }

        if ($request->isPost()) {
            $postData = $request->getPost()->toArray();

            if (isset($postData['updatePersonalData'])) {
                array_shift($postData);

                $this->updatePersonalData($postData);
            } elseif (isset($postData['mail'])) {
                array_shift($postData);

                $this->sendMail($postData);
            }
        }

        // user info
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs =  $qb
            ->select(array('u', 'ua'))
            ->from(self::USER_ENTITY, 'u')
            ->join(
                self::USER_ADDITION_ENTITY, 'ua',
                'WITH', 'u.id = ua.idUser'
            )
            ->where('u.id = ?1')
            ->setParameter(1, $this->currentUser)
            ->getQuery();

        $qr = $qs->getArrayResult();

        // последние 5 покупок
        $purchasedProducts = $this->getPurchasedProducts();

        $deliveryMethod = $this->getEntityManager()->getRepository(self::DELIVERY_METHOD)->findAll();
        $paymentMethod  = $this->getEntityManager()->getRepository(self::PAYMENT_METHOD)->findAll();

        return new ViewModel(array(
            'userInfo'          => $qr,
            'deliveryMethod'    => $deliveryMethod,
            'paymentMethod'     => $paymentMethod,
            'purchasedProducts' => $purchasedProducts
        ));
    }

    /**
     * @param int $limit
     *
     * @return mixed
     */
    protected function getPurchasedProducts($limit = 5)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs =  $qb
            ->select(array('r.date as date', 'rt.price as price', 'p'))
            ->from(self::REGISTER_ENTITY, 'r')
            ->join(
                self::REGISTER_TABLE_ENTITY, 'rt',
                'WITH', 'r.id = rt.idRegister'
            )
            ->join(
                self::PRODUCT_ENTITY, 'p',
                'WITH', 'rt.idProduct = p.id'
            )
            ->where('rt.idUser = ?1')
            ->setParameter(1, $this->currentUser)
            ->groupBy('r.date', 'p.name')
            ->orderBy('rt.id', 'desc')
            ->setMaxResults($limit)
            ->getQuery();

        $qr = $qs->getArrayResult();

        return $qr;
    }

    /**
     * @param $postData
     */
    protected function updatePersonalData($postData)
    {
        array_walk($postData, function(&$item) {
            $item = strip_tags($item);
        });

        $userExists = $this->checkUserExistsByPhone($postData['userPhone']);

        if ($userExists) {
            $this->mergeUserData($userExists);
        }

        $user           = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findOneBy(array('id' => $this->currentUser));
        $userAddition   = $this->getEntityManager()->getRepository(self::USER_ADDITION_ENTITY)->findOneBy(array('idUser' => $this->currentUser));

        if (!empty($postData['userName'])) {
            $user->setUsername($postData['userName']);

            $this->getEntityManager()->persist($user);
        }

        if (!empty($postData['userPhone'])) {
            $userAddition->setPhone($postData['userPhone']);

            $this->getEntityManager()->persist($userAddition);
        }

        if (!empty($postData['userAddress'])) {
            $userAddition->setAddress($postData['userAddress']);

            $this->getEntityManager()->persist($userAddition);
        }

        $this->getEntityManager()->flush();

        $this->prg('/personal-cabinet', true);
    }

    /*
     * @todo add send mail
     */
    protected function sendMail($postData)
    {
        var_dump($postData);
    }

    /**
     * @param $phone
     *
     * @return bool
     */
    protected function checkUserExistsByPhone($phone)
    {
        $user = $this->getEntityManager()->getRepository(self::USER_ADDITION_ENTITY)->findOneBy(array('phone' => $phone));

        if (!empty($user)) {
            return $user;
        }

        return false;
    }

    /**
     * @param $oldUser
     */
    protected function mergeUserData($oldUser)
    {
        // update cart
        $qb = $this->getEntityManager()->createQueryBuilder();
        $idOldUser = $oldUser->getIdUser()->getId();

        $qu = $qb->update(self::CART_ENTITY, 'c')
            ->set('c.idUser', '?1')
            ->where('c.idUser = ?2')
            ->setParameter(1, $this->currentUser)
            ->setParameter(2, $idOldUser)
            ->getQuery();
        $qu->execute();

        // update user
        $oldTotalBuy = $oldUser->getTotalBuy();

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qu = $qb->update(self::USER_ADDITION_ENTITY, 'ua')
            ->set('ua.totalBuy', '?1')
            ->set('ua.checked', '?2')
            ->where('ua.idUser = ?3')
            ->setParameter(1, $oldTotalBuy)
            ->setParameter(2, true)
            ->setParameter(3, $this->currentUser)
            ->getQuery();
        $qu->execute();

        //remove tmp user
        $removeUser = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findOneBy(array('id' => $idOldUser));

        $this->getEntityManager()->persist($removeUser);
        $this->getEntityManager()->remove($removeUser);
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