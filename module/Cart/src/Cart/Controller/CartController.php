<?php

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Cart\Entity\CartEntity;

class CartController extends AbstractActionController
{
    public function indexAction()
    {
        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();
        $idUser = (int) $currentUser;
        
        $em = $this
            ->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager');

        if ($idUser) {
            $cart = $em
                ->getRepository('\Cart\Entity\CartEntity')
                ->findOneBy(array('idUser' => $idUser));
        } else {
            /** todo
             * 1. Сделать создание корзины для
             * неаутентифицированного пользователя (гостя).
             * 2. Подумать, как делать отдельные корзины для разных гостей.
             * 3. Не забыть про уничтожение корзины после ухода гостя.
             */
        }

        if ($cart instanceof CartEntity) {
            $cartTable = $em
                ->getRepository('\Cart\Entity\CartTable')
                ->findBy(array('idCartEntity' => $cart->getId()));
        }

        $view = new ViewModel(array(
            'cart' => $cart,
            'cartTable' => $cartTable,
            'currentUser' => $currentUser,
        ));

        return $view;
    }
}