<?php
return array(
    'doctrine' => array(
        'driver' => array(
            'catalog_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Catalog/Entity')
            ),
            'brand_entity' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Catalog/Entity')
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
            'Catalog\Controller\Index'   => 'Catalog\Controller\IndexController',
            'Catalog\Controller\Brand'   => 'Catalog\Controller\BrandController',
            'Catalog\Controller\Catalog' => 'Catalog\Controller\CatalogController',
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
            'brand' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/brand[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Catalog\Controller',
                        'controller'    => 'Brand',
                        'action'        => 'index',
                    ),
                ),
            ),
            'category' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/category[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Catalog\Controller',
                        'controller'    => 'Catalog',
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