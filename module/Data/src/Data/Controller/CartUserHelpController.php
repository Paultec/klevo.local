<?php
namespace Data\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\User;
use Zend\Authentication\AuthenticationService;

class CartUserHelpController extends AbstractActionController
{
    const USER_ENTITY           = 'User\Entity\User';
    const USER_ADDITION_ENTITY  = 'User\Entity\UserAddition';

    protected $em;
    private $currentUser;
    private $registered = false;

    public function __construct()
    {
        $auth = new AuthenticationService();
        $this->currentUser = $auth->getIdentity();

        if (!is_null($this->currentUser)) {
            $this->registered = true;
        }
    }

    /**
     * @return array
     */
    public function indexAction()
    {
        // параметры, полученные через dispatch
        $postData = $this->params('postData');

        // если пользователь не авторизован, проверить, нет ли уже его в базе
        if (is_null($this->currentUser)) {
            $userCheck = $this->checkUserExists($postData['phone']);

            // если пользователя нет - создаем временного
            if (!$userCheck) {
                $tmpEmail   = 'temp' . md5(microtime() . range(0, 99999) . md5(microtime())) . '@klevo.com.ua';
                $tmpPass    = '$2y$14$t.oX7G/7sCHzao7ieg7ouOoCrlBkw5KTATTzsJuWcNkCr72zaFMdS';

                $tmpUser = new User();

                $tmpUser->setEmail($tmpEmail);
                $tmpUser->setPassword($tmpPass);

                $this->getEntityManager()->persist($tmpUser);
                $this->getEntityManager()->flush();

                $this->currentUser = (int)$tmpUser->getId();
            }
        }

        // получить текущего пользователя
        $user               = $this->getEntityManager()->find(self::USER_ENTITY, $this->currentUser);
        $userAddition       = $this->getEntityManager()->getRepository(self::USER_ADDITION_ENTITY)->findOneBy(array('idUser' => $user->getId()));
        $noUserChange       = false;
        $changeIdUserInCart = array();

        // проверить, есть ли пользователь с таким же номером, если он зарегистрирован
        if (!$userAddition->getChecked() && $this->registered) {
            $tmpUserAddition = $this->getEntityManager()->getRepository(self::USER_ADDITION_ENTITY)->findOneBy(array('phone' => $postData['phone']));

            if (!is_null($tmpUserAddition)) {
//                $tmpUser = $this->getEntityManager()->find(self::USER_ENTITY, $tmpUserAddition->getId());

                // Обновить данные
                $userAddition->setPhone($tmpUserAddition->getPhone());
                $userAddition->setTotalBuy($tmpUserAddition->getTotalBuy());
                $userAddition->setAddress($tmpUserAddition->getAddress());
                $userAddition->setChecked(1);

                $changeIdUserInCart['from'] = $tmpUserAddition->getId();
                $changeIdUserInCart['to']   = $userAddition->getId();

                $this->getEntityManager()->persist($userAddition);
//                $this->getEntityManager()->remove($tmpUserAddition);
//                $this->getEntityManager()->remove($tmpUser);
            }

            $userAddition->setChecked(true);
            $this->getEntityManager()->flush();
        }

        // проверяем, не изменил ли пользователь свои данные (номер телефона, адрес доставки)
        if ($userAddition->getPhone() != $postData['phone']) {
            $userAddition->setPhone($postData['phone']);

            $noUserChange = !$noUserChange;
        }

        if (!isset($postData['oneClickBuy']) && $userAddition->getAddress() != $postData['address']) {
            $userAddition->setAddress($postData['address']);

            $noUserChange = !$noUserChange;
        }

        // записать новые данные пользователя, если они есть
        if ($noUserChange) {
            $this->getEntityManager()->persist($userAddition);
            $this->getEntityManager()->flush();
        }

        $userArray = array(
            'user'               => $user,
            'userAddition'       => $userAddition,
            'changeIdUserInCart' => $changeIdUserInCart
        );

        return $userArray;
    }

    /**
     * Remove temp user
     */
    public function removeTmpUserAction()
    {
        // удалить временно пользователя
        $userId = $this->params('postData');

        $tmpUser = $this->getEntityManager()->find(self::USER_ENTITY, $userId);

        $this->getEntityManager()->remove($tmpUser);
        $this->getEntityManager()->flush();
    }

    /**
     * Get user info (phone, address)
     *
     * @return array
     */
    public function userAction()
    {
        if (!is_null($this->currentUser)) {
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
            $qs->execute();

            $qr = $qs->getArrayResult();

            $userInfo = array(
                'phone'     => $qr[1]['phone'],
                'address'   => $qr[1]['address']
            );

            return $userInfo;
        }
    }

    /**
     * @param $phone
     *
     * @return bool
     */
    protected function checkUserExists($phone)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs =  $qb
            ->select(array('u.id', 'ua'))
            ->from(self::USER_ENTITY, 'u')
            ->join(
                self::USER_ADDITION_ENTITY, 'ua',
                'WITH', 'u.id = ua.idUser'
            )
            ->where(
                $qb->expr()->in('ua.phone', $phone)
            )
            ->getQuery();

        $qr = $qs->getArrayResult();

        if (!empty($qr)) {
            $this->currentUser = $qr[0]['id'];

            return true;
        }

        return false;
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