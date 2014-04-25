<?php
return array(
    'router' => array(
        'routes' => array(
            'product' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/product',
                    'defaults' => array(
                        'controller' => 'Product\Controller\Index',
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
                'may_terminate' => true,
                'child_routes' => array(
                    'success' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/success',
                            'defaults' => array(
                                'controller'    => 'Product\Controller\Upload',
                                'action'        => 'success',
                            ),
                        ),
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