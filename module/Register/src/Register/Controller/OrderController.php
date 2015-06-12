<?php

namespace Register\Controller;

use Register\Entity\Register;
use Register\Entity\RegisterTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\DateTime;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Paginator\Paginator;

use Register\Model\Order;
use Register\Entity\Order as OrderEntity;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use GoSession;

class OrderController extends AbstractActionController
{
    const OPERATION_ENTITY      = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY   = 'Data\Entity\PaymentType';
    const STATUS_ENTITY         = 'Data\Entity\Status';
    const STORE_ENTITY          = 'Data\Entity\Store';
    const USER_ENTITY           = 'User\Entity\User';
    const REGISTER_ENTITY       = 'Register\Entity\Register';
    const REGISTER_TABLE_ENTITY = 'Register\Entity\RegisterTable';
    const ORDER_ENTITY          = 'Register\Entity\Order';
    const ORDER_TABLE_ENTITY    = 'Register\Entity\OrderTable';
    const PRODUCT_ENTITY        = 'Product\Entity\Product';

    /**
     * @var
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $filter = array();
        $data = array(
            'store'       => array(),
            'operation'   => array(),
            'paymentType' => array(),
            'status'      => array(),
            'user'        => array(),
        );

        $currentSession = new Container();
        unset($currentSession->seoUrlParams);

        $request = $this->getRequest();

        if ($request->getPost()) {
            if ($request->getPost('beginDateReset')) {
                unset($currentSession->beginDate);
            }
            if ($request->getPost('beginDate')) {
                $filter['beginDate'] = $request->getPost('beginDate');
                $currentSession->beginDate = $filter['beginDate'];
            } elseif ($currentSession->beginDate) {
                $filter['beginDate'] = $currentSession->beginDate;
            }

            if ($request->getPost('endDateReset')) {
                unset($currentSession->endDate);
            }
            if ($request->getPost('endDate')) {
                $filter['endDate'] = $request->getPost('endDate');
                $currentSession->endDate = $filter['endDate'];
            } elseif ($currentSession->endDate) {
                $filter['endDate'] = $currentSession->endDate;
            }

            if ($request->getPost('storeReset')) {
                unset($currentSession->storeFrom);
                unset($currentSession->idStoreFrom);
            }
            if ($request->getPost('store')) {
                $filter['store'] = $request->getPost('store');
                $currentSession->store = $filter['store'];
                $idStore = $request->getPost('idStore');
                $currentSession->idStore = $idStore;
            } elseif ($currentSession->storeFrom) {
                $filter['store'] = $currentSession->store;
                $idStore = $currentSession->idStore;
            }

            if ($request->getPost('statusReset')) {
                unset($currentSession->status);
                unset($currentSession->idStatus);
            }
            if ($request->getPost('status')) {
                $filter['status'] = $request->getPost('status');
                $currentSession->status = $filter['status'];
                $idStatus = $request->getPost('idStatus');
                $currentSession->idStatus = $idStatus;
            } elseif ($currentSession->status) {
                $filter['status'] = $currentSession->status;
                $idStatus = $currentSession->idStatus;
            }

            if ($request->getPost('userReset')) {
                unset($currentSession->user);
                unset($currentSession->idUser);
            }
            if ($request->getPost('user')) {
                $filter['user'] = $request->getPost('user');
                $currentSession->user = $filter['user'];
                $idUser = $request->getPost('idUser');
                $currentSession->idUser = $idUser;
            } elseif ($currentSession->user) {
                $filter['user'] = $currentSession->user;
                $idUser = $currentSession->idUser;
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('o')
            ->from(self::ORDER_ENTITY, 'o');

        if (isset($filter['beginDate'])) {
            $qb->andWhere('o.date >= :beginDate')
                ->setParameter('beginDate', $filter['beginDate']);
        }

        if (isset($filter['endDate'])) {
            $qb->andWhere('o.date <= :endDate')
                ->setParameter('endDate', $filter['endDate']);
        }

        if (isset($filter['storeFrom'])) {
            $qb->andWhere('o.idStore = :idStore')
                ->setParameter('idStore', $idStore);
        }

        if (isset($filter['status'])) {
            $qb->andWhere('o.idStatus = :idStatus')
                ->setParameter('idStatus', $idStatus);
        }

        if (isset($filter['user'])) {
            $qb->andWhere('o.idUser = :idUser')
                ->setParameter('idUser', $idUser);
        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qb));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(20);
        // End pagination

        // оставляю в $data для отображения только те данные, которые присутствуют в записях Order
        $order = $qb->getQuery()->getResult();
        if (count($order) > 0) {
            foreach ($order as $item) {
                if (!in_array($item->getIdStore(), $data['store'])) {
                    $data['store'][] = $item->getIdStore();
                }
                if (!in_array($item->getIdStatus(), $data['status'])) {
                    $data['status'][] = $item->getIdStatus();
                }
                if (!in_array($item->getIdUser(), $data['user'])) {
                    $data['user'][] = $item->getIdUser();
                }
            }
        }

        return new ViewModel(array(
            'paginator' => $paginator,
            'filter'    => $filter,
            'data'      => $data,
        ));
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $currentSession = new Container();
        unset($currentSession->idBrand);
        unset($currentSession->idCatalog);
        unset($currentSession->idRegister);
        unset($currentSession->productList);

        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();

        $form = $this->getForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $order = new OrderEntity();
            $form->setData($request->getPost());

            if ($form->isValid()){
                $formData = $form->getData();

                $idStore     = $this->getEntityManager()->find(self::STORE_ENTITY, $formData['idStore']);
                $idStatus    = $this->getEntityManager()->find(self::STATUS_ENTITY, $formData['idStatus']);
                $currentUser = $this->getEntityManager()->find(self::USER_ENTITY, $currentUser);

                $order
                    ->setDate(new \DateTime($formData['date']))
                    ->setIdStore($idStore)
                    ->setIdStatus($idStatus)
                    ->setIdUser($currentUser);

                $this->getEntityManager()->persist($order);
                $this->getEntityManager()->flush();

                $idOrder = $order->getId();

                return $this->forward()->dispatch('Register/Controller/OrderTable',
                    array('action' => 'index', 'content' => $idOrder));
            }
        }

        return new ViewModel(array(
            'form'        => $form,
            'store'       => $this->setOptionItems(self::STORE_ENTITY, true),
            'status'      => $this->setOptionItems(self::STATUS_ENTITY),
        ));
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function addToRegisterAction()
    {
        $statusClosed = 6;

        $request = $this->getRequest();

        if ($request->isPost()) {
            $postData = $request->getPost('idOrder');

            $order = $this->getEntityManager()->find(self::ORDER_ENTITY, $postData);

            $isRegistered = $order->getIsRegistered();

            if ($isRegistered) {
                return $this->redirect()->toRoute('order');
            }

            $order->setIdStatus($this->getEntityManager()->find(self::STATUS_ENTITY, $statusClosed));
            $order->setIsRegistered(true);

            $this->getEntityManager()->flush();

            $currentSession = new Container();
            $currentSession->idOrder    = $order->getId();
            $currentSession->storeFrom  = $order->getIdStore()->getId();

            return $this->prg('/register/add', true);
        }

        return $this->redirect()->toRoute('order');
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Order();
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
    }

    /**
     * @param      $entity
     * @param bool $attrib
     *
     * @return array
     */
    protected function setOptionItems($entity, $attrib = false)
    {
        $optionList = $this->getEntityManager()->getRepository($entity)->findAll();

        $optionArray = array();

        for ($i = 0, $option = count($optionList); $i < $option; $i++) {
            $optionArray[$i]['id']      = $optionList[$i]->getId();
            $optionArray[$i]['name']    = $optionList[$i]->getName();

            if ($attrib) {
                $optionArray[$i]['attrib']  = $optionList[$i]->getIdAttrib()->getId();
            }
        }

        array_unshift($optionArray, array(
            'id'   => null,
            'name' => 'Выберите из списка'
        ));

        return $optionArray;
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