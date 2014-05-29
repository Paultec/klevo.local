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

use Doctrine\ORM\EntityManager;
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
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
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
     * @return array
     */
    protected function setOptionItems($entity)
    {
        $optionList = $this->getEntityManager()
            ->getRepository($entity)->findAll();

        $optionArray = array();

        for ($i = 0, $option = count($optionList); $i < $option; $i++) {
            $optionArray[$i]['id']   = $optionList[$i]->getId();
            $optionArray[$i]['name'] = $optionList[$i]->getName();
        }

        array_unshift($optionArray, array(
            'id'   => null,
            'name' => 'Выберите из списка'
        ));

        return $optionArray;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $filter = array();
        $data = array();

        $request = $this->getRequest();

        $currentSession = new Container();

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
                list($filter['storeFrom'], $idStoreFrom) = explode('#', $request->getPost('storeFrom'));
                $currentSession->storeFrom = $filter['storeFrom'];
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
                $filter['idStoreTo'] = $request->getPost('idStoreTo');
                $currentSession->idStoreTo = $filter['idStoreTo'];
            } elseif ($currentSession->storeTo) {
                $filter['storeTo'] = $currentSession->storeTo;
                $filter['idStoreTo'] = $currentSession->idStoreTo;
            }

            if ($request->getPost('operationReset')) {
                unset($currentSession->operation);
            }
            if ($request->getPost('operation')) {
                $filter['operation'] = $request->getPost('operation');
                $currentSession->operation = $filter['operation'];
            } elseif ($currentSession->operation) {
                $filter['operation'] = $currentSession->operation;
            }

            if ($request->getPost('paymentTypeReset')) {
                unset($currentSession->paymentType);
            }
            if ($request->getPost('paymentType')) {
                $filter['paymentType'] = $request->getPost('paymentType');
                $currentSession->paymentType = $filter['paymentType'];
            } elseif ($currentSession->paymentType) {
                $filter['paymentType'] = $currentSession->paymentType;
            }

            if ($request->getPost('statusReset')) {
                unset($currentSession->status);
            }
            if ($request->getPost('status')) {
                $filter['status'] = $request->getPost('status');
                $currentSession->status = $filter['status'];
            } elseif ($currentSession->status) {
                $filter['status'] = $currentSession->status;
            }

            if ($request->getPost('userReset')) {
                unset($currentSession->user);
            }
            if ($request->getPost('user')) {
                $filter['user'] = $request->getPost('user');
                $currentSession->user = $filter['user'];
            } elseif ($currentSession->user) {
                $filter['user'] = $currentSession->user;
            }
        }

        $storeList = $this->getEntityManager()->getRepository(self::STORE_ENTITY)->findAll();
        $data['storeFrom'] = $storeList;
        $data['storeTo'] = $storeList;

        $operationList = $this->getEntityManager()->getRepository(self::OPERATION_ENTITY)->findAll();
        $data['operation'] = $operationList;

        $paymentTypeList = $this->getEntityManager()->getRepository(self::PAYMENT_TYPE_ENTITY)->findAll();
        $data['paymentType'] = $paymentTypeList;

        $statusList = $this->getEntityManager()->getRepository(self::STATUS_ENTITY)->findAll();
        $data['status'] = $statusList;

        $userList = $this->getEntityManager()->getRepository(self::USER_ENTITY)->findAll();
        $data['user'] = $userList;

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

//        if (isset($filter['storeFrom'])) {
//            $qb->andWhere('r.idStoreFrom = :idStoreFrom')
//                ->setParameter('idStoreFrom', $request->getPost('idStoreFrom'));
//        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qb));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(20);

        //$register = $this->getEntityManager()->getRepository(self::REGISTER_ENTITY)->findAll();

        return new ViewModel(array(
            'paginator' => $paginator,
            'filter'    => $filter,
            'data'      => $data,
        ));
    }

    /**
     * @return
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

        if ($request->isPost()){

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
            'storeFrom'   => $this->setOptionItems(self::STORE_ENTITY),
            'storeTo'     => $this->setOptionItems(self::STORE_ENTITY),
            'operation'   => $this->setOptionItems(self::OPERATION_ENTITY),
            'paymentType' => $this->setOptionItems(self::PAYMENT_TYPE_ENTITY),
            'status'      => $this->setOptionItems(self::STATUS_ENTITY),
        ));
    }
}