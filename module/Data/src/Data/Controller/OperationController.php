<?php

namespace Data\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OperationController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }


}

