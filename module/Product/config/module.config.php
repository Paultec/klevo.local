<?php
return array(
    'router' => array(
        'routes' => array(
            'product' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/product',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Product\Controller',
                        'controller'    => 'index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'index',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'view' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:name].html',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'name'       => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'index',
                                'action'        => 'view',
                            ),
                        ),
                    ),
                    'seoUrl' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[/:param1][/:param2][/:page]',
                            'constraints' => array(
                                'param1' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'param2' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'page'   => '[0-9]+',
                            ),
                            'defaults' => array(
                                'param1' => '',
                                'param2' => '',
                            )
                        ),
                    ),
                ),
            ),
            'editproduct' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/edit-product[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller'    => 'Product\Controller\Edit',
                        'action'        => 'index',
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