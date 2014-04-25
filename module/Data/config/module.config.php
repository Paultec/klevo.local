<?php
return array(
    'router' => array(
        'routes' => array(
            'data' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/data',
                    'defaults' => array(
                        'controller' => 'Data\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'attribute' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/attribute[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Data\Controller\Attribute',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'operation' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/operation[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Data\Controller\Operation',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'paymentType' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/paymentType[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Data\Controller\PaymentType',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'status' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/status[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Data\Controller\Status',
                                'action'     => 'index',
                            ),
                        ),
                    ),
                    'store' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/store[/:action]',
                            'constraints' => array(
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Data\Controller\Store',
                                'action'     => 'index',
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
            'attribute_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Data/Entity'),
            ),
            'operation_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Data/Entity'),
            ),
            'paymentType_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Data/Entity'),
            ),
            'status_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Data/Entity'),
            ),
            'store_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '/../src/Data/Entity'),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Data\Entity' => 'attribute_entity',
                ),
            ),
        ),
    ),
);