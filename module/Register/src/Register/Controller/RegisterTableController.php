<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RegisterTableController extends AbstractActionController
{
    public function indexAction()
    {
        $idRegister = $this->params('content');

        $product = $this->forward()->dispatch('Product\Controller\Edit',
            array('action' => 'index', 'externalCall' => true));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res =  new ViewModel(array(
            'idRegister' => $idRegister,
            'product'    => $product,
            'type'       => 'register-table'
        ));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    public function addAction()
    {
        return new ViewModel();
    }

}

