<?php

return array(
    'bjyauthorize' => array(
        'default_role' => 'guest',

        'identity_provider' => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',

        'role_providers'    => array(
            // using an object repository (entity repository) to load all roles into our ACL
            'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                'object_manager'    => 'doctrine.entitymanager.orm_default',
                'role_entity_class' => 'User\Entity\Role',
            ),
        ),

        'guards'            => array(
            'BjyAuthorize\Guard\Controller' => array(
                // Раздел 1
                // разрешения для всех посетителей сайта (включая незарегистрированных)
                array(
                    'controller' => 'Application\Controller\Index',
                    'action'     => array('index'),
                    'roles'      => array(),
                ),
                array(
                    'controller' => 'Product\Controller\Index',
                    'action'     => array('index', 'view'),
                    'roles'      => array(),
                ),
                array(
                    'controller' => 'Cart\Controller\Cart',
                    'action'     => array('index', 'add', 'success'),
                    'roles'      => array(),
                ),
                array(
                    'controller' => 'Search\Controller\Index',
                    'action'     => array('index'),
                    'roles'      => array(),
                ),
                // конец раздела 1

                // Раздел 2
                // разграничение доступа к модулю ZfcUser для неавторизовавшихся и авторизовавшихся
                array(
                    'controller' => 'zfcuser',
                    'action'     => array('index', 'login', 'authenticate','register'),
                    'roles'      => array('guest'),
                ),
                array(
                    'controller' => 'zfcuser',
                    'action'     => array('logout', 'changepassword', 'changeemail'),
                    'roles'      => array('user'),
                ),
                // конец раздела 2

                // Раздел 3
                // разрешения для авторизованных пользователей с ролью USER

                // конец раздела 3

                // Раздел 4
                // разрешения для авторизованных пользователей с ролью MANAGER
                array(
                    'controller' => array(
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
                        'Data\Controller\DeliveryMethod',
                        'Data\Controller\PaymentMethod',
                        'Product\Controller\Edit',
                        'Product\Controller\Index',
                        'Product\Controller\Parse',
                        'Product\Controller\Upload',
                        'Register\Controller\Index',
                        'Register\Controller\Register',
                        'Register\Controller\RegisterTable',
                        'Register\Controller\Remains',
                    ),
                    'roles'      => array('manager'),
                ),
                // конец раздела 4

                // Раздел 5
                // разрешения для авторизованных пользователей с ролью ADMIN
                array(
                    'controller' => array(
                        'Register\Controller\Balance',
                        'Register\Controller\Order',
                        'Register\Controller\Payment',
                    ),
                    'roles'      => array('admin'),
                ),
                // конец раздела 5

                // Раздел 6
                // глобальные разрешения для авторизованных пользователей с ролью BOSS
                array(
                    'controller' => array(
                        'Register\Controller\User',
                    ),
                    'roles'      => array('boss'),
                ),
                // конец раздела 6
            ),
        ),

    ),
);