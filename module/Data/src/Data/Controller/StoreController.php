<?php

namespace Data\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class StoreController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }


}

