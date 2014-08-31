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

                $cache = $sm->getServiceLocator()->get('filesystem');
                $controller->setCache($cache);

                return $controller;
            },
    ),
);