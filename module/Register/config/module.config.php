<?php
return array(
    'router' => array(
        'routes' => array(
            'register' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/register',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Register\Controller',
                        'controller'    => 'register',
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
                                'controller'    => 'register',
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
                                'controller'    => 'register',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'add' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'register',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller'    => 'register',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            'register-table' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/register-table[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\RegisterTable',
                        'action'     => 'index',
                    ),
                ),
            ),
            'balance' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/balance[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\Balance',
                        'action'     => 'index',
                    ),
                ),
            ),
            'order' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/order[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),
            'payment' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/payment[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\Payment',
                        'action'     => 'index',
                    ),
                ),
            ),
            'remains' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/remains[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\Remains',
                        'action'     => 'index',
                    ),
                ),
            ),
            'user' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/user[/:action][/]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Register\Controller\User',
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