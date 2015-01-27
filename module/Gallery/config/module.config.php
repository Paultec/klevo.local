<?php

return array(
    'router' => array(
        'routes' => array(
            'gallery' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/gallery[/:action][/:page]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'    => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Gallery\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'edit-gallery' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/edit-gallery[/:action]',
                    'constraints' => array(
                        'action'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Gallery\Controller\Edit',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Gallery\Controller\Edit' => 'Gallery\Controller\EditController',
        ),
        'factories' => array(
            'Gallery\Controller\Index' => function($sm) {
                $controller = new Gallery\Controller\IndexController();

                $serviceLocator = $sm->getServiceLocator();

                $services = array(
                    'cache' => $serviceLocator->get('filesystem'),
                );

                foreach ($services as $serviceKey => $serviceValue) {
                    $setter = 'set' . ucfirst($serviceKey);

                    $controller->$setter($serviceValue);
                }

                return $controller;
            },
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'gallery_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Gallery/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Gallery\Entity' => 'gallery_entity',
                ),
            ),
        ),
    ),
);