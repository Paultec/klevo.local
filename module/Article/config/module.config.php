<?php

return array(
    'router' => array(
        'routes' => array(
            'article' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/article',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Article\Controller',
                        'controller'    => 'index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:page]',
                            'constraints' => array(
                                'page' => '[0-9]+',
                            ),
                            'defaults' => array(
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
                                'name'       => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'index',
                                'action'     => 'view',
                            ),
                        ),
                    ),
                ),
            ),
            'edit-article' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/edit-article',
                    'defaults' => array(
                        'controller' => 'Article\Controller\Edit',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
            'article-img-upload' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/article-img-upload[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Article\Controller',
                        'controller'    => 'Upload',
                        'action'        => 'index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Article\Controller\Index'   => 'Article\Controller\IndexController',
            'Article\Controller\Upload'  => 'Article\Controller\UploadController',
        ),
        'factories' => array(
            'Article\Controller\Edit' => function($sm) {
                $controller = new Article\Controller\EditController();

                $serviceLocator = $sm->getServiceLocator();

                $services = array(
                    'translit' => $serviceLocator->get('translitService'),
                    'cache'    => $serviceLocator->get('filesystem'),
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
        'template_map' => array(
            'image/layout' => __DIR__ . '/../view/layout/image.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'article_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Article/Entity'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Article\Entity' => 'article_entity',
                ),
            ),
        ),
    ),
);