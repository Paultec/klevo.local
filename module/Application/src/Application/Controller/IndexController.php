<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function indexAction()
    {
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