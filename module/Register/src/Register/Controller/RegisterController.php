<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\DateTime;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Paginator\Paginator;

use Register\Model\Register;
use Register\Entity\Register as RegisterEntity;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use GoSession;

class RegisterController extends AbstractActionController
{
    const OPERATION_ENTITY    = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY = 'Data\Entity\PaymentType';
    const STATUS_ENTITY       = 'Data\Entity\Status';
    const STORE_ENTITY        = 'Data\Entity\Store';
    const USER_ENTITY         = 'User\Entity\User';
    const REGISTER_ENTITY     = 'Register\Entity\Register';

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
            'storeFrom'   => array(),
            'storeTo'     => array(),
            'operation'   => array(),
            'paymentType' => array(),
            'status'      => array(),
            'user'        => array(),
        );

        $request = $this->getRequest();

        $currentSession = new Container();
        //var_dump($currentSession->seoUrlParams);
        unset($currentSession->seoUrlParams);

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

            if ($request->getPost('storeFromReset')) {
                unset($currentSession->storeFrom);
                unset($currentSession->idStoreFrom);
            }
            if ($request->getPost('storeFrom')) {
                $filter['storeFrom'] = $request->getPost('storeFrom');
                $currentSession->storeFrom = $filter['storeFrom'];
                $idStoreFrom = $request->getPost('idStoreFrom');
                $currentSession->idStoreFrom = $idStoreFrom;
            } elseif ($currentSession->storeFrom) {
                $filter['storeFrom'] = $currentSession->storeFrom;
                $idStoreFrom = $currentSession->idStoreFrom;
            }

            if ($request->getPost('storeToReset')) {
                unset($currentSession->storeTo);
                unset($currentSession->idStoreTo);
            }
            if ($request->getPost('storeTo')) {
                $filter['storeTo'] = $request->getPost('storeTo');
                $currentSession->storeTo = $filter['storeTo'];
                $idStoreTo = $request->getPost('idStoreTo');
                $currentSession->idStoreTo = $idStoreTo;
            } elseif ($currentSession->storeTo) {
                $filter['storeTo'] = $currentSession->storeTo;
                $idStoreTo = $currentSession->idStoreTo;
            }

            if ($request->getPost('operationReset')) {
                unset($currentSession->operation);
                unset($currentSession->idOperation);
            }
            if ($request->getPost('operation')) {
                $filter['operation'] = $request->getPost('operation');
                $currentSession->operation = $filter['operation'];
                $idOperation = $request->getPost('idOperation');
                $currentSession->idOperation = $idOperation;
            } elseif ($currentSession->operation) {
                $filter['operation'] = $currentSession->operation;
                $idOperation = $currentSession->idOperation;
            }

            if ($request->getPost('paymentTypeReset')) {
                unset($currentSession->paymentType);
                unset($currentSession->idPaymentType);
            }
            if ($request->getPost('paymentType')) {
                $filter['paymentType'] = $request->getPost('paymentType');
                $currentSession->paymentType = $filter['paymentType'];
                $idPaymentType = $request->getPost('idPaymentType');
                $currentSession->idPaymentType = $idPaymentType;
            } elseif ($currentSession->paymentType) {
                $filter['paymentType'] = $currentSession->paymentType;
                $idPaymentType = $currentSession->idPaymentType;
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
        $qb->add('select', 'r')
            ->add('from', 'Register\Entity\Register r');

        if (isset($filter['beginDate'])) {
            $qb->andWhere('r.date >= :beginDate')
                ->setParameter('beginDate', $filter['beginDate']);
        }

        if (isset($filter['endDate'])) {
            $qb->andWhere('r.date <= :endDate')
                ->setParameter('endDate', $filter['endDate']);
        }

        if (isset($filter['storeFrom'])) {
            $qb->andWhere('r.idStoreFrom = :idStoreFrom')
                ->setParameter('idStoreFrom', $idStoreFrom);
        }

        if (isset($filter['storeTo'])) {
            $qb->andWhere('r.idStoreTo = :idStoreTo')
                ->setParameter('idStoreTo', $idStoreTo);
        }

        if (isset($filter['operation'])) {
            $qb->andWhere('r.idOperation = :idOperation')
                ->setParameter('idOperation', $idOperation);
        }

        if (isset($filter['paymentType'])) {
            $qb->andWhere('r.idPaymentType = :idPaymentType')
                ->setParameter('idPaymentType', $idPaymentType);
        }

        if (isset($filter['status'])) {
            $qb->andWhere('r.idStatus = :idStatus')
                ->setParameter('idStatus', $idStatus);
        }

        if (isset($filter['user'])) {
            $qb->andWhere('r.idUser = :idUser')
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

        // оставляю в $data для отображения только те данные, которые присутствуют в записях Register
        $query = $qb->getQuery();
        $register = $query->getResult();
        if (count($register) > 0) {
            foreach ($register as $item) {
                if (!in_array($item->getIdStoreFrom(), $data['storeFrom'])) {
                    $data['storeFrom'][] = $item->getIdStoreFrom();
                }
                if (!in_array($item->getIdStoreTo(), $data['storeTo'])) {
                    $data['storeTo'][] = $item->getIdStoreTo();
                }
                if (!in_array($item->getIdOperation(), $data['operation'])) {
                    $data['operation'][] = $item->getIdOperation();
                }
                if (!in_array($item->getIdPaymentType(), $data['paymentType'])) {
                    $data['paymentType'][] = $item->getIdPaymentType();
                }
                if (!in_array($item->getIdStatus(), $data['status'])) {
                    $data['status'][] = $item->getIdStatus();
                }
                if (!in_array($item->getIdUser(), $data['user'])) {
                    $data['user'][] = $item->getIdUser();
                }
            }
        }

        //$storeList = $this->getEntityManager()->getRepository(self::STORE_ENTITY)->findAll();
        //$data['storeFrom'] = $storeList;
        //$data['storeTo'] = $storeList;

        //$operationList = $this->getEntityManager()->getRepository(self::OPERATION_ENTITY)->findAll();
        //$data['operation'] = $operationList;

        //$paymentTypeList = $this->getEntityManager()->getRepository(self::PAYMENT_TYPE_ENTITY)->findAll();
        //$data['paymentType'] = $paymentTypeList;

        //$statusList = $this->getEntityManager()->getRepository(self::STATUS_ENTITY)->findAll();
        //$data['status'] = $statusList;

        //$userList = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findAll();
        //$data['user'] = $userList;

        return new ViewModel(array(
            'paginator' => $paginator,
            'filter'    => $filter,
            'data'      => $data,
        ));
    }

    /**
     * @return mixed|ViewModel
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
            $registerNote = new RegisterEntity();
            $form->setData($request->getPost());

            if ($form->isValid()){

                $formData = $form->getData();

                $date = new \DateTime($formData['date']);
                $formData['date'] = $date;

                $formData['idStoreFrom'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $formData['idStoreFrom']);
                $formData['idStoreTo'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $formData['idStoreTo']);
                $formData['idOperation'] = $this->getEntityManager()->
                    find(self::OPERATION_ENTITY, $formData['idOperation']);
                $formData['idPaymentType'] = $this->getEntityManager()->
                    find(self::PAYMENT_TYPE_ENTITY, $formData['idPaymentType']);
                $formData['idStatus'] = $this->getEntityManager()->
                    find(self::STATUS_ENTITY, $formData['idStatus']);
                $formData['idUser'] = $this->getEntityManager()->
                    find(self::USER_ENTITY, $currentUser);

                $registerNote->populate($formData);

                $this->getEntityManager()->persist($registerNote);
                $this->getEntityManager()->flush();

                $idRegister = $registerNote->getId();

                return $this->forward()->dispatch('Register/Controller/RegisterTable',
                    array('action' => 'index', 'content' => $idRegister));
            }
        }

        return new ViewModel(array(
            'form'        => $form,
            'storeFrom'   => $this->setOptionItems(self::STORE_ENTITY, true),
            'storeTo'     => $this->setOptionItems(self::STORE_ENTITY, true),
            'operation'   => $this->setOptionItems(self::OPERATION_ENTITY),
            'paymentType' => $this->setOptionItems(self::PAYMENT_TYPE_ENTITY),
            'status'      => $this->setOptionItems(self::STATUS_ENTITY),
        ));
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()){
            $idRegister = $request->getPost('idRegister');
            $register   = $this->getEntityManager()->find(self::REGISTER_ENTITY, $idRegister);
            $form       = $this->getForm();
        }

        return new ViewModel(array(
            'register' => $register,
            'form'     => $form,
        ));
    }

    /**
     * @param $entity
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
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Register();
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
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