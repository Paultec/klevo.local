<?php

namespace Cart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Cart\Entity\CartEntity;
use GoSession;

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

        $currentSession = new Container();
        $conteiner = $currentSession->idProduct;
        //unset($currentSession->idProduct);

        $view = new ViewModel(array(
            'cart' => $cart,
            'cartTable' => $cartTable,
            'currentUser' => $currentUser,
            'conteiner' => $conteiner,
        ));

        return $view;
    }

    public function addAction()
    {
        $request = $this->getRequest()->getPost();

        if ($request->idProduct) {
            //Если в POST пришел id товара, открываю контейнер сессии
            $currentSession = new Container();
            if ($currentSession->idProduct) {
                //Если в контейнере уже есть товары, записываю их
                $productInCart = $currentSession->idProduct;
                //Получаю id товара из POST
                $currentProduct = $request->idProduct;
                if (in_array($currentProduct, $productInCart)) {
                    //Если такой товар уже есть в корзине, ухожу
                    $this->redirect()->toRoute('product');
                } else {
                    //Если нет - добавляю товар в корзину
                    $productInCart[] = $currentProduct;
                }
            } else {
                //Товаров в корзине не было - создаю новый массив и записываю id товара из POST
                $productInCart[] = $request->idProduct;
            }
            //Записываю id товаров в контейнер
            $currentSession->idProduct = $productInCart;
        }

        $this->redirect()->toRoute('product');
        //return new ViewModel(array('request'=>$request));
    }
}