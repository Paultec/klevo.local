<?php
/**
 * Global navigation config
 */

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Галерея',
                'route' => 'gallery',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Статьи',
                'route' => 'article',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Форум',
                'route' => 'home',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Оплата и доставка',
                'route' => 'paymentdelivery',
                'class' => 'float-shadow',
            ),
            array(
                'label' => 'Контакты',
                'route' => 'contacts',
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