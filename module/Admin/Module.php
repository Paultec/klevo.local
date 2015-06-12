<?php
namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {

            $controller     = $e->getTarget();
            $controllerName = $e->getRouteMatch()->getParam('controller');

            if (in_array($controllerName, array(
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
                    'Product\Controller\Parse',
                    'Product\Controller\Upload',
                    'Register\Controller\Index',
                    'Register\Controller\Register',
                    'Register\Controller\RegisterTable',
                    'Register\Controller\Remains',
                    'Register\Controller\Payment',
                    'Register\Controller\Balance',
                    'Article\Controller\Edit',
                    'Article\Controller\Upload',
                    'Gallery\Controller\Edit',
                    'Register\Controller\Order',
                    'Register\Controller\OrderTable',
                    'User\Controller\Edit'
                ))) {
                $controller->layout('admin/layout');
            }
        });

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function($e) {
            // new active order
            $cache = $e->getApplication()->getServiceManager()->get('filesystem');

            if ($cache->hasItem('active-order')) {
                $e->getViewModel()->setVariable('activeOrder', 'active');
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
