<?php

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class CartController extends AbstractActionController
{
    public function indexAction()
    {
        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();
        $idUser = $currentUser;
        $em = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');
        $cart = $em
            ->getRepository('\Cart\Entity\CartEntity')
            ->findOneBy(array('idUser' => $idUser));
        $cartTable = $em
            ->getRepository('\Cart\Entity\CartTable')
            ->findBy(array('idCartEntity' => $cart->getId()));
        $view = new ViewModel(array(
            'cart' => $cart,
            'cartTable' => $cartTable,
            'currentUser' => $currentUser,
        ));
        return $view;
    }
}