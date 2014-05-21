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
                                'controller'    => 'Register',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),

//            'register-add' => array(
//                'type'    => 'Literal',
//                'options' => array(
//                    'route'    => '/register-add',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'Register\Controller',
//                        'controller'    => 'register',
//                        'action'        => 'add',
//                    ),
//                ),
//            ),

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