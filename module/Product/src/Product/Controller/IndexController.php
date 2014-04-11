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

        $this->getServiceLocator()->get('filesystem')->setItem('foo', 'bar');
        $sl = $this->getServiceLocator()->get('filesystem')->getItem('foo');
        var_dump($sl);

        return new ViewModel(array(
            'productList' => $productList,
        ));
    }


}

