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
//        вот так контроллер вызывается:
        $product = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));
//        а так Ошибка 404 "Запрашиваемый контроллер не смог отправить запрос."
//        $product = $this->forward()->dispatch('Catalog\Controller\Index');
//        и так Ошибка 404 "Запрашиваемый контроллер не смог отправить запрос."
//        $product = $this->forward()->dispatch('Product\Controller\Index', array('action' => 'index'));
//        и так тоже
//        $product = $this->forward()->dispatch('Product\Controller\Index');
//        а так вызывается
//        $product = $this->forward()->dispatch('Product\Controller\Upload', array('action' => 'index'));
//        а так опять 404
//        $product = $this->forward()->dispatch('Product\Controller\Upload');
        return new ViewModel(array('idRegister' => $idRegister, 'product' => $product));
    }


}

