<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

class BalanceController extends AbstractActionController
{
    const STORE_ENTITY    = 'Data\Entity\Store';
    const REGISTER_ENTITY = 'Register\Entity\Register';
    const PAYMENT_ENTITY  = 'Register\Entity\Payment';

    protected $em;
    protected $idAttrib     = 3; // supplier
    protected $idOperation  = 4; // return
    protected $idInnerShop  = 1;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->clearPreviousFilter();

        $suppliersData = array();
        $suppliersId   = array();

        $suppliers = $this->getEntityManager()->getRepository(self::STORE_ENTITY)
            ->findBy(array('idAttrib' => $this->idAttrib));

        $count = 1;
        foreach ($suppliers as $supplier) {
            $suppliersId[] = $supplier->getId();
            $suppliersData[$count]['name'] = $supplier->getName();

            $count++;
        }

        $registers = $this->getEntityManager()->getRepository(self::REGISTER_ENTITY)
            ->findBy(array('idStoreFrom' => $suppliersId));

        $payments = $this->getEntityManager()->getRepository(self::PAYMENT_ENTITY)
            ->findBy(array('idStore' => $suppliersId));

        $returns = $this->getEntityManager()->getRepository(self::REGISTER_ENTITY)
            ->findBy(array('idOperation' => $this->idOperation));

        $outgoing = array();
        foreach ($payments as $payment) {
            $name = $payment->getIdStore()->getName();

            if (isset($outgoing[$name])) {
                $outgoing[$name]['outgoing'] += $payment->getAmount();
            } else {
                $outgoing[$name]['id']       = $payment->getIdStore()->getId();
                $outgoing[$name]['outgoing'] = $payment->getAmount();
            }
        }

        $incoming = array();
        foreach ($registers as $register) {
            $name = $register->getIdStoreFrom()->getName();

            if (isset($incoming[$name])) {
                $incoming[$name]['incoming'] += $register->getTotalSum();
            } else {
                if (!isset($incoming[$name]['id'])) {
                    $outgoing[$name]['id'] = $register->getIdStoreFrom()->getId();
                }

                $incoming[$name]['incoming'] = $register->getTotalSum();
            }
        }

        $return = array();
        foreach ($returns as $returnItem) {
            $from = $returnItem->getIdStoreFrom()->getIdAttrib()->getId();
            $name = $returnItem->getIdStoreTo()->getName();

            if ($from != $this->idInnerShop) { continue; }

            if (isset($return[$name])) {
                $return[$name]['returns'] += $returnItem->getTotalSum();
            } else {
                $return[$name]['returns'] = $returnItem->getTotalSum();
            }
        }

        return new ViewModel(array(
            'result' => array_merge_recursive($outgoing, $incoming, $return)
        ));
    }

    /**
     * Clear previous filters
     */
    protected function clearPreviousFilter()
    {
        $currentSession = new Container();

        unset($currentSession->beginDate);
        unset($currentSession->endDate);
        unset($currentSession->storeTo);
        unset($currentSession->idStoreTo);
        unset($currentSession->storeFrom);
        unset($currentSession->idStoreFrom);
        unset($currentSession->operation);
        unset($currentSession->idOperation);
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