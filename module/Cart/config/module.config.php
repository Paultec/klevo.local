<?php
return array(
    'router' => array(
        'routes' => array(
            'cart' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/cart',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Cart\Controller',
                        'controller'    => 'cart',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:action[/:translit]]',
                            'constraints' => array(
                                'action'   => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'translit' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                                'action'  => 'index',
                            ),
                        ),
                    ),
                ),
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
            'cart_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Cart/Entity'),
            ),
            'cart_table' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Cart/Entity'),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Cart\Entity' => 'cart_entity',
                ),
            ),
        ),
    ),
);