<?php

return array(
    'router' => array(
        'routes' => array(
            'gallery' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/gallery',
                    'defaults' => array(
                        'controller' => 'Gallery\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Gallery\Controller\Index' => 'Gallery\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);