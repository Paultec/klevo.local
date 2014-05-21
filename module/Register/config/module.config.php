<?php
return array(
    'router' => array(
        'routes' => array(
            'register' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/register[/:action]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\Register',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'pager' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:page]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'Register\Controller\Register',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),


//                'type'    => 'Literal',
//                'options' => array(
//                    'route'    => '/register',
//                    'defaults' => array(
//                        'controller' => 'Register\Controller\Register',
//                        'action'     => 'index',
//                    ),
//                ),
//                'may_terminate' => true,
//                'child_routes' => array(
//                    'default' => array(
//                        'type'    => 'Segment',
//                        'options' => array(
//                            'route'    => '/[:controller[/:action]]',
//                            'constraints' => array(
//                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//                            ),
//                            'defaults' => array(
//                                'controller'    => 'Register\Controller\Register',
//                                'action'        => 'index',
//                            ),
//                        ),
//                    ),
//
//                    'pager' => array(
//                        'type'    => 'Segment',
//                        'options' => array(
//                            'route'    => '[/:page]',
//                            'constraints' => array(
//                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//                            ),
//                            'defaults' => array(
//                                'controller'    => 'Register\Controller\Register',
//                                'action'        => 'index',
//                            ),
//                        ),
//                    ),
//                ),
            ),
            'register-table' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/register-table[/][/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\RegisterTable',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'Requesthelper' => 'Register\View\Helper\Factory\RequestHelperFactory',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'register_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Register/Entity'),
            ),
            'registerTable_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Register/Entity'),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Register\Entity' => 'register_entity',
                ),
            ),
        ),
    ),
);