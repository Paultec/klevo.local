<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegisterTableController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $idRegister = $this->params('content');

        $product = $this->forward()->dispatch('Product\Controller\Index',
            array('action' => 'index', 'externalCall' => true));

        $res =  new ViewModel(array(
            'idRegister' => $idRegister
        ));

        $res->addChild($product, 'product');

        return $res;

    }

}

