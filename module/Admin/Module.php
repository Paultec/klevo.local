<?php
namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    private $adminControllers = array(
        'Admin\Controller\Index',
        'Catalog\Controller\Brand',
        'Catalog\Controller\Catalog',
        'Catalog\Controller\Index',
        'Data\Controller\Attribute',
        'Data\Controller\Index',
        'Data\Controller\Operation',
        'Data\Controller\PaymentType',
        'Data\Controller\Status',
        'Data\Controller\Store',
        'Product\Controller\Edit',
        'Product\Controller\Index',
        'Product\Controller\Parse',
        'Product\Controller\Upload',
        'Register\Controller\Index',
        'Register\Controller\Register',
        'Register\Controller\RegisterTable'
    );

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {
            $controller = $e->getTarget();
            $controllerName = $controller->getEvent()
                ->getRouteMatch()->getParam('controller');

            if (in_array($controllerName, $this->adminControllers)) {
                $controller->layout('admin/layout');
            }
        });
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getControllerConfig()
    {
        return include __DIR__ . '/config/controllers.config.php';
    }
}
