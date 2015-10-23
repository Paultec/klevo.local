<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'gallery' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/gallery',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'gallery',
                    ),
                ),
            ),
            'article' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/article',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'article',
                    ),
                ),
            ),
            'paymentdelivery' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/payment-delivery',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'payment-delivery',
                    ),
                ),
            ),
            'contacts' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/contacts',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contacts',
                    ),
                ),
            ),
            'feedback' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/feedback',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'feedback',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
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
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'translitService'         => 'Application\Service\TranslitService',
            'fullNameCategoryService' => 'Application\Service\FullNameCategoryService'
        ),
        'factories' => array(
            // Интегрировать Доктрину в сервис
            'fullNameService' => function($sm) {
                    $service = new Application\Service\FullNameCategoryService;
                    $service->setEntityManager($sm->get('doctrine.entitymanager.orm_default'));

                    return $service;
            }
        ),
        'abstract_factories' => array(
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
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
    'controllers' => array(
        'factories' => array(
            'Application\Controller\Index' => function($sm) {
                $controller = new Application\Controller\IndexController();

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
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
