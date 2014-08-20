<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $routeParam = $this->params()->fromRoute();
        //var_dump($routeParam);
        //$currentSession = new Container();
        //var_dump($currentSession);

        $index = new ViewModel();

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');
        $index->addChild($catalog, 'catalog');

        return $index;
    }

    /**
     * @return ViewModel
     */
    public function paymentDeliveryAction()
    {
        return new ViewModel();
    }

    /**
     * @return ViewModel
     */
    public function contactsAction()
    {
        return new ViewModel();
    }
}