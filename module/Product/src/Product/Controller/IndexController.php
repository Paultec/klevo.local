<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Cart;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $em = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        $productList = $em
            ->getRepository('\Product\Entity\Product')
            ->findBy(array());

        return new ViewModel(array(
            'productList' => $productList,
        ));
    }


}

