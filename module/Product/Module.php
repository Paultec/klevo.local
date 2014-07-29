<?php
namespace Product;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach('dispatch', function($e) {
                $controller     = $e->getTarget();
                $controllerName = $controller->getEvent()->getRouteMatch()
                    ->getParam('controller');

                if (!in_array($controllerName, array('Product\Controller\Index'))) {
                    $currentSession = new Container();

                    var_dump($currentSession->seoUrlParams);

                    if (!empty($currentSession->seoUrlParams)) {
                        foreach ($currentSession->seoUrlParams as $key => $value) {
                            unset($currentSession->seoUrlParams[$key]);
                        }

                        unset($currentSession->flag);
                    }

                    var_dump($currentSession->seoUrlParams);
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
