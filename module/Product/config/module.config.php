<?php
return array(
    'router' => array(
        'routes' => array(
            'edit' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/edit-product',
                    'defaults' => array(
                        'controller' => 'Product\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'producer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/producer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Product\Controller\Producer',
                        'action'     => 'index',
                    ),
                ),
            ),
            'product' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/product[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Product\Controller\Product',
                        'action'     => 'index',
                    ),
                ),
            ),
            'fileupload' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/file-upload',
                    'defaults' => array(
                        'controller'    => 'Product\Controller\Upload',
                        'action'        => 'index',
                    ),
                ),
            ),
            'parse' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/parse',
                    'defaults' => array(
                        'controller' => 'Product\Controller\Parse',
                        'action'     => 'index',
                    ),
                ),
            ),

        ),
    ),
    'translator' => array(
        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'product_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Product/Entity'),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Product\Entity' => 'product_entity',
                ),
            ),
        ),
    ),
);