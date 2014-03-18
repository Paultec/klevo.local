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
                'paths' => array(__DIR__ . '\..\src\Data\Attribute'),
            ),
            'operation_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '\..\src\Data\Operation'),
            ),
            'paymentType_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '\..\src\Data\PaymentType'),
            ),
            'status_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '\..\src\Data\Status'),
            ),
            'store_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => array(__DIR__ . '\..\src\Data\Store'),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Data\Entity' => 'attribute_entity',
                ),
            ),
        ),
    ),
);