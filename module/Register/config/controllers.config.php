<?php

return array(
    'invokables' => array(
        'Register\Controller\Balance'       => 'Register\Controller\BalanceController',
        'Register\Controller\Index'         => 'Register\Controller\IndexController',
        'Register\Controller\Order'         => 'Register\Controller\OrderController',
        'Register\Controller\Payment'       => 'Register\Controller\PaymentController',
        'Register\Controller\Register'      => 'Register\Controller\RegisterController',
        //'Register\Controller\RegisterTable' => 'Register\Controller\RegisterTableController',
        'Register\Controller\Remains '      => 'Register\Controller\RemainsController',
        'Register\Controller\User'          => 'Register\Controller\UserController',
    ),
    'factories' => array(
        'Register\Controller\RegisterTable' => function($sm) {
                $controller = new Register\Controller\RegisterTableController();

                $cache = $sm->getServiceLocator()->get('filesystem');
                $controller->setCache($cache);

                return $controller;
            },
    ),
);