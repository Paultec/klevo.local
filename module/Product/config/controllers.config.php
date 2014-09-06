<?php

return array(
    'invokables' => array(
        'Product\Controller\Index'  => 'Product\Controller\IndexController',
        'Product\Controller\Upload' => 'Product\Controller\UploadController',
        'Product\Controller\Parse'  => 'Product\Controller\ParseController'
    ),
    'factories' => array(
        'Product\Controller\Edit' => function($sm) {
                $controller = new Product\Controller\EditController();

                $serviceLocator = $sm->getServiceLocator();

                $services = array(
                    'cache'    => $serviceLocator->get('filesystem'),
                    'translit' => $serviceLocator->get('translitService'),
                    'fullName' => $serviceLocator->get('fullNameService')
                );

                foreach ($services as $serviceKey => $serviceValue) {
                    /**
                     * $cache    = $serviceLocator->get('filesystem');
                     * $translit = $serviceLocator->get('translitService');
                     * $fullName = $serviceLocator->get('fullNameService');
                     *
                     * $controller->setCache($cache);
                     * $controller->setTranslit($translit);
                     * $controller->setFullName($fullName);
                     */

                    $setter = 'set' . ucfirst($serviceKey);

                    $controller->$setter($serviceValue);
                }

                return $controller;
            },
    ),
);