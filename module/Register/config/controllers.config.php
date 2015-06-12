<?php

return array(
    'invokables' => array(
        'Register\Controller\Balance'  => 'Register\Controller\BalanceController',
        'Register\Controller\Index'    => 'Register\Controller\IndexController',
        'Register\Controller\Order'    => 'Register\Controller\OrderController',
        'Register\Controller\Payment'  => 'Register\Controller\PaymentController',
        'Register\Controller\Register' => 'Register\Controller\RegisterController',
        'Register\Controller\Remains ' => 'Register\Controller\RemainsController',
        'Register\Controller\User'     => 'Register\Controller\UserController',
    ),
    'factories' => array(
        'Register\Controller\RegisterTable' => function($sm) {
                $controller = new Register\Controller\RegisterTableController();

                $serviceLocator = $sm->getServiceLocator();

                $services = array(
                    'cache'    => $serviceLocator->get('filesystem'),
                    'fullName' => $serviceLocator->get('fullNameService')
                );

                foreach ($services as $serviceKey => $serviceValue) {
                    $setter = 'set' . ucfirst($serviceKey);

                    $controller->$setter($serviceValue);
                }

                return $controller;
        },
        'Register\Controller\OrderTable' => function($sm) {
            $controller = new Register\Controller\OrderTableController();

            $serviceLocator = $sm->getServiceLocator();

            $services = array(
                'cache'    => $serviceLocator->get('filesystem'),
                'fullName' => $serviceLocator->get('fullNameService')
            );

            foreach ($services as $serviceKey => $serviceValue) {
                $setter = 'set' . ucfirst($serviceKey);

                $controller->$setter($serviceValue);
            }

            return $controller;
        },
    ),
);