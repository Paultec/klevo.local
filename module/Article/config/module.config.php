<?php

return array(
    'router' => array(
        'routes' => array(
            'article' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/article',
                    'defaults' => array(
                        'controller' => 'Article\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Article\Controller\Index' => 'Article\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);