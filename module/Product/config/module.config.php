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
                    'pager' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:page]',
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
                            'route'    => '[/:action[/:id[/]]]',
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
                    'seoUrl' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[/:brand][/:category][/:page]',
                            'constraints' => array(
                                'brand' => '[a-zA-Z0-9_-]+',
                                'category' => '[a-zA-Z0-9_-]+',
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'brand'  => 0,
                                'category'  => 0,
                                'page'  => 0,
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
    'view_helpers' => array(
        'factories' => array(
            'Requesthelper' => 'Product\View\Helper\Factory\RequestHelperFactory',
        )
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