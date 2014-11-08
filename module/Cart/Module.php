<?php
namespace Cart;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function(MvcEvent $e) {
                // роль пользователя в системе
                $currentSession = new Container();

                $itemsInCart = 0;

                if (isset($currentSession->cart)) {
                    $itemsInCart = count($currentSession->cart);
                }

                // к-во товаров в корзине
                $e->getViewModel()->setVariable('itemsInCart', $itemsInCart);
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
