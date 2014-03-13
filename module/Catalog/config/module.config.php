<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'catalog_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '\..\src\Catalog\Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Catalog\Entity' => 'catalog_entity',
                )
            )
        )
    ),

    'controllers' => array(
        'invokables' => array(
            'Catalog\Controller\Index' => 'Catalog\Controller\IndexController',
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'showList' => 'Catalog\View\Helper\ShowList',
        ),
    ),

    'router' => array(
        'routes' => array(
            'catalog' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/catalog',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Catalog\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'catalog'                   => __DIR__ . '/../view',
        ),
    ),

);