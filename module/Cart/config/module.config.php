<?php
return array(
    'router' => array(
        'routes' => array(
            'cart' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/cart[/][:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Cart\Controller\Cart',
                        'action'     => 'index',
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
                'paths' => array(__DIR__ . '/../src/Cart/Entity'),
            ),
            'cart_table' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
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