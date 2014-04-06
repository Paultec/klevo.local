<?php
/**
 * Global navigation config
 */

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Галерея',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Статьи',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Форум',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Оплата и доставка',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Контакты',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
);