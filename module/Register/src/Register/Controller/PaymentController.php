<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\DateTime;
use Zend\Session\Container;
use Zend\Paginator\Paginator;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use Register\Entity\Payment;

class PaymentController extends AbstractActionController
{
    const PAYMENT_ENTITY = 'Register\Entity\Payment';
    const STORE_ENTITY   = 'Data\Entity\Store';

    protected $em;
    protected $idSupplier = 3;

    public function indexAction()
    {
        $filter = array();
        $data = array(
            'store' => array(),
        );

        $request = $this->getRequest();
        $currentSession = new Container();

        unset($currentSession->seoUrlParams);

        if ($request->getPost()) {
            if ($request->getPost('beginDateReset')) {
                unset($currentSession->beginDate);
            }
            if ($request->getPost('beginDate')) {
                $filter['beginDate']        = $request->getPost('beginDate');
                $currentSession->beginDate  = $filter['beginDate'];
            } elseif ($currentSession->beginDate) {
                $filter['beginDate'] = $currentSession->beginDate;
            }

            if ($request->getPost('endDateReset')) {
                unset($currentSession->endDate);
            }
            if ($request->getPost('endDate')) {
                $filter['endDate']          = $request->getPost('endDate');
                $currentSession->endDate    = $filter['endDate'];
            } elseif ($currentSession->endDate) {
                $filter['endDate'] = $currentSession->endDate;
            }

            if ($request->getPost('storeReset')) {
                unset($currentSession->store);
                unset($currentSession->idStore);
            }
            if ($request->getPost('store')) {
                $filter['store']        = $request->getPost('store');
                $currentSession->store  = $filter['store'];
                $idStore = $request->getPost('idStore');
                $currentSession->idStore = $idStore;
            } elseif ($currentSession->store) {
                $filter['store']    = $currentSession->store;
                $idStore            = $currentSession->idStore;
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('p'))
                ->from(self::PAYMENT_ENTITY, 'p');

        if (isset($filter['beginDate'])) {
            $qb->andWhere('p.date >= :beginDate')
                ->setParameter('beginDate', $filter['beginDate']);
        }

        if (isset($filter['endDate'])) {
            $qb->andWhere('p.date <= :endDate')
                ->setParameter('endDate', $filter['endDate']);
        }

        if (isset($filter['store'])) {
            $qb->andWhere('p.idStore = :idStore')
                ->setParameter('idStore', $idStore);
        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qb));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(20);

        $payment = $qb->getQuery()->getResult();
        if (count($payment) > 0) {
            foreach ($payment as $item) {
                if (!in_array($item->getIdStore(), $data['store'])) {
                    $data['store'][] = $item->getIdStore();
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
        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost()->toArray();

            $store = $this->getEntityManager()->getRepository(self::STORE_ENTITY)
                ->findOneBy(array('id' => $postData['store']));

            $payment = new Payment();

            $payment->setDate(new \DateTime($postData['date']))
                    ->setAmount($postData['amount'] * 100)
                    ->setIdStore($store)
                    ->setComment($postData['comment'] ?: null);

            $this->getEntityManager()->persist($payment);
            $this->getEntityManager()->flush();

            $this->redirect()->toRoute('payment');
        }

        $payments = $this->getEntityManager()->getRepository(self::PAYMENT_ENTITY)->findAll();

        return new ViewModel(array(
            'payments'  => $payments,
            'suppliers' => $this->setOptionItems(self::STORE_ENTITY)
        ));
    }

    public function editAction()
    {
        return new ViewModel();
    }

    /**
     * @param $entity
     *
     * @return array
     */
    protected function setOptionItems($entity)
    {
        $optionList = $this->getEntityManager()->getRepository($entity)
            ->findBy(array('idAttrib' => $this->idSupplier));

        $optionArray = array();

        for ($i = 0, $option = count($optionList); $i < $option; $i++) {
            $optionArray[$i]['id']      = $optionList[$i]->getId();
            $optionArray[$i]['name']    = $optionList[$i]->getName();
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