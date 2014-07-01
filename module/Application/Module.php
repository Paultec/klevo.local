<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function(MvcEvent $e) {
                // роль пользователя в системе
                $auth = new AuthenticationService();
                $currentUser = $auth->getIdentity();

                $userRole = 'guest';

                if (!is_null($currentUser)) {
                    $user = $e->getApplication()
                        ->getServiceManager()->get('Doctrine\ORM\EntityManager')
                        ->find('User\Entity\User', $currentUser)->getRoles();

                    $userRole = $user[0]->getRoleId();
                }

                // установка роли в layout
                $e->getViewModel()->setVariable('userRole', $userRole);
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
}
