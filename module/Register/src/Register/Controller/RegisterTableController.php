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


}

