<?php
namespace Product;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        // Удалить ключи из сессии для очистки seoUrl параметров
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {
                $currentRoute = $e->getRouteMatch()->getMatchedRouteName();

                $match = strpos($currentRoute, 'product');

                if ($match === false) {
                    $currentSession = new Container();

                    if (!empty($currentSession->seoUrlParams)) {
                        foreach ($currentSession->seoUrlParams as $key => $value) {
                            unset($currentSession->seoUrlParams[$key]);
                        }

                        unset($currentSession->flag);
                    }
                }
            }, 100);
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
