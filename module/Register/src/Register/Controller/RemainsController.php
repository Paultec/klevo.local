<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

use Register\Entity\RegisterTable as RegisterTableEntity;

class RemainsController extends AbstractActionController
{
    const ATTRIBUTE_ENTITY          = 'Data\Entity\Attribute';
    const OPERATION_ENTITY          = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY       = 'Data\Entity\PaymentType';
    const STATUS_ENTITY             = 'Data\Entity\Status';
    const STORE_ENTITY              = 'Data\Entity\Store';
    const USER_ENTITY               = 'User\Entity\User';
    const REGISTER_ENTITY           = 'Register\Entity\Register';
    const REGISTER_TABLE_ENTITY     = 'Register\Entity\RegisterTable';
    const PRODUCT_ENTITY            = 'Product\Entity\Product';
    const PRODUCT_QTY_ENTITY        = 'Product\Entity\ProductCurrentQty';
    const BRAND_ENTITY              = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY           = 'Catalog\Entity\Catalog';

    const MY_STORE_ATTRIBUTE = 1;
    /**
     * @var
     */
    protected $em = null;

    protected $fullName = null;

    public function indexAction()
    {
        $filter = array();
        $data = array(
            'storeFrom'   => array(),
            'storeTo'     => array(),
            'catalog'     => array(),
            'brand'       => array(),
            'operation'   => array(),
            'paymentType' => array(),
            'status'      => array(),
            'user'        => array(),
        );

        $request = $this->getRequest();

        $currentSession = new Container();

        if ($request->getPost()) {

            if ($request->getPost('dateReset')) {
                unset($currentSession->date);
            }
            if ($request->getPost('date')) {
                $filter['date'] = $request->getPost('date');
                $currentSession->date = $filter['date'];
            } elseif ($currentSession->date) {
                $filter['date'] = $currentSession->date;
            }

            if ($request->getPost('storeFromReset')) {
                unset($currentSession->storeFrom);
                unset($currentSession->idStoreFrom);
            }
            if ($request->getPost('storeFrom')) {
                $filter['storeFrom'] = $request->getPost('storeFrom');
                $currentSession->storeFrom = $filter['storeFrom'];
                $idStoreFrom = $request->getPost('idStoreFrom');
                $currentSession->idStoreFrom = $idStoreFrom;
            } elseif ($currentSession->storeFrom) {
                $filter['storeFrom'] = $currentSession->storeFrom;
                $idStoreFrom = $currentSession->idStoreFrom;
            }

            if ($request->getPost('storeToReset')) {
                unset($currentSession->storeTo);
                unset($currentSession->idStoreTo);
            }
            if ($request->getPost('storeTo')) {
                $filter['storeTo'] = $request->getPost('storeTo');
                $currentSession->storeTo = $filter['storeTo'];
                $idStoreTo = $request->getPost('idStoreTo');
                $currentSession->idStoreTo = $idStoreTo;
            } elseif ($currentSession->storeTo) {
                $filter['storeTo'] = $currentSession->storeTo;
                $idStoreTo = $currentSession->idStoreTo;
            }

            if ($request->getPost('catalogReset')) {
                unset($currentSession->catalog);
                unset($currentSession->idCatalog);
            }
            if ($request->getPost('catalog')) {
                $filter['catalog'] = $request->getPost('catalog');
                $currentSession->catalog = $filter['catalog'];
                $idCatalog = $request->getPost('idCatalog');
                $currentSession->idCatalog = $idCatalog;
            } elseif ($currentSession->catalog) {
                $filter['catalog'] = $currentSession->catalog;
                $idCatalog = $currentSession->idCatalog;
            }

            if ($request->getPost('brandReset')) {
                unset($currentSession->brand);
                unset($currentSession->idBrand);
            }
            if ($request->getPost('brand')) {
                $filter['brand'] = $request->getPost('brand');
                $currentSession->brand = $filter['brand'];
                $idBrand = $request->getPost('idBrand');
                $currentSession->idBrand = $idBrand;
            } elseif ($currentSession->brand) {
                $filter['brand'] = $currentSession->brand;
                $idBrand = $currentSession->idBrand;
            }

            if ($request->getPost('operationReset')) {
                unset($currentSession->operation);
                unset($currentSession->idOperation);
            }
            if ($request->getPost('operation')) {
                $filter['operation'] = $request->getPost('operation');
                $currentSession->operation = $filter['operation'];
                $idOperation = $request->getPost('idOperation');
                $currentSession->idOperation = $idOperation;
            } elseif ($currentSession->operation) {
                $filter['operation'] = $currentSession->operation;
                $idOperation = $currentSession->idOperation;
            }

            if ($request->getPost('paymentTypeReset')) {
                unset($currentSession->paymentType);
                unset($currentSession->idPaymentType);
            }
            if ($request->getPost('paymentType')) {
                $filter['paymentType'] = $request->getPost('paymentType');
                $currentSession->paymentType = $filter['paymentType'];
                $idPaymentType = $request->getPost('idPaymentType');
                $currentSession->idPaymentType = $idPaymentType;
            } elseif ($currentSession->paymentType) {
                $filter['paymentType'] = $currentSession->paymentType;
                $idPaymentType = $currentSession->idPaymentType;
            }

            if ($request->getPost('statusReset')) {
                unset($currentSession->status);
                unset($currentSession->idStatus);
            }
            if ($request->getPost('status')) {
                $filter['status'] = $request->getPost('status');
                $currentSession->status = $filter['status'];
                $idStatus = $request->getPost('idStatus');
                $currentSession->idStatus = $idStatus;
            } elseif ($currentSession->status) {
                $filter['status'] = $currentSession->status;
                $idStatus = $currentSession->idStatus;
            }

            if ($request->getPost('userReset')) {
                unset($currentSession->user);
                unset($currentSession->idUser);
            }
            if ($request->getPost('user')) {
                $filter['user'] = $request->getPost('user');
                $currentSession->user = $filter['user'];
                $idUser = $request->getPost('idUser');
                $currentSession->idUser = $idUser;
            } elseif ($currentSession->user) {
                $filter['user'] = $currentSession->user;
                $idUser = $currentSession->idUser;
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'a')
           ->add('from', 'Data\Entity\Attribute a')
           ->add('where', 'a.id = :id')
           ->setParameter('id', self::MY_STORE_ATTRIBUTE);
        $query = $qb->getQuery();
        $myAttribute = $query->getResult();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 's')
           ->add('from', 'Data\Entity\Store s')
           ->add('where', 's.idAttrib = :idAttribute')
           ->setParameter('idAttribute', $myAttribute);
        $query = $qb->getQuery();
        $myStore = $query->getResult();

        $allRegister    = array();
        $remainsByStore = array();

        foreach ($myStore as $store) {
            // формирую запрос на выборку входящих документов по каждому складу
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->add('select', 'r')
               ->add('from', 'Register\Entity\Register r')
               ->andWhere('r.idStoreTo = :idStore')
               ->setParameter('idStore', $store)
               ->add('orderBy', 'r.date');

            if (isset($filter['date'])) {
                $qb->andWhere('r.date <= :date')
                   ->setParameter('date', $filter['date']);
            }

            $query = $qb->getQuery();
            $registerIn = $query->getResult();

            // формирую запрос на выборку исходящих документов по каждому складу
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->add('select', 'r')
               ->add('from', 'Register\Entity\Register r')
               ->andWhere('r.idStoreFrom = :idStore')
               ->setParameter('idStore', $store)
               ->add('orderBy', 'r.date');

            if (isset($filter['date'])) {
                $qb->andWhere('r.date <= :date')
                   ->setParameter('date', $filter['date']);
            }

            $query = $qb->getQuery();
            $registerOut = $query->getResult();

            // получаю все записи с приходом товаров
            foreach ($registerIn as $register) {
                $allRegister[] = $register;
                $qb = $this->getEntityManager()->createQueryBuilder();
                $qb->add('select', 'rt')
                   ->add('from', 'Register\Entity\RegisterTable rt')
                   ->andWhere('rt.idRegister = :idRegister')
                   ->setParameter('idRegister', $register);

                // если установлены фильтры по категории и/или бренду - отбираем только эти товары
                if (isset($filter['catalog']) || isset($filter['brand'])) {
                    $qbFilter = $this->getEntityManager()->createQueryBuilder();

                    $qbFilter->add('select', 'p')
                             ->add('from', 'Product\Entity\Product p');

                    if (isset($filter['catalog'])) {
                        $qbFilter->andWhere('p.idCatalog = :idCatalog')
                                 ->setParameter('idCatalog', $idCatalog);
                    }
                    if (isset($filter['brand'])) {
                        $qbFilter->andWhere('p.idBrand = :idBrand')
                                 ->setParameter('idBrand', $idBrand);
                    }

                    $queryFilter = $qbFilter->getQuery();
                    $actualProduct = $queryFilter->getResult();

                    $listProduct = array();
                    foreach ($actualProduct as $item) {
                        $listProduct[] = $item->getId();
                    }

                    $qb->andWhere($qb->expr()->in('rt.idProduct', $listProduct));
                }

                $query = $qb->getQuery();
                $registerTableIn = $query->getResult();

                while ($registerTableIn) {
                    $product = array_shift($registerTableIn);
                    $remainsByStore[$store->getId()][$product->getIdProduct()->getId()][] = $product;
                }
            }

            // получаю все записи с расходом товаров
            foreach ($registerOut as $register) {
                $allRegister[] = $register;

                $qb = $this->getEntityManager()->createQueryBuilder();
                $qb->add('select', 'rt')
                    ->add('from', 'Register\Entity\RegisterTable rt')
                    ->andWhere('rt.idRegister = :idRegister')
                    ->setParameter('idRegister', $register);

                // если установлены фильтры по категории и/или бренду - отбираем только эти товары
                if (isset($filter['catalog']) || isset($filter['brand'])) {
                    $qb->andWhere($qb->expr()->in('rt.idProduct', $listProduct));
                }

                $query = $qb->getQuery();
                $registerTableOut = $query->getResult();

                // вычитаю расход товара из прихода товара, пока не закончится расход
                while ($registerTableOut) {
                    $product = array_shift($registerTableOut);
                    $qtyOut = $product->getQty();

                    while ($qtyOut > 0) {
                        $firstIncome = array_shift($remainsByStore[$store->getId()][$product->getIdProduct()->getId()]);

                        if ($firstIncome->getQty() > $qtyOut) {
                            $qtyIncome = $firstIncome->getQty();
                            $firstIncome->setQty($qtyIncome - $qtyOut);
                            array_unshift($remainsByStore[$store->getId()][$product->getIdProduct()->getId()], $firstIncome);
                            $qtyOut = 0;
                        } elseif ($firstIncome->getQty() == $qtyOut) {
                            $qtyOut = 0;
                        } else {
                            $qtyOut = $qtyOut - $firstIncome->getQty();
                        }
                    }
                }
            }
        }

        // если установлен фильтр по складу, оставляем только товары с этого склада
        if (isset($filter['storeTo'])) {
            $temp = $remainsByStore[$idStoreTo];
            unset($remainsByStore);
            $remainsByStore[$idStoreTo] = $temp;
        }

        // TO DO
        // если установлен фильтр по поставщику, оставляем только товары этого поставщика
        if (isset($filter['storeFrom'])) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->add('select', 'r')
               ->add('from', 'Register\Entity\Register r')
               ->andWhere('r.idStoreFrom = :idStore')
               ->setParameter('idStore', $idStoreFrom);

            $query = $qb->getQuery();
            $register = $query->getResult();

            $registerFrom = array();
            foreach ($register as $registerNote) {
                $registerFrom[] = $registerNote->getId();
            }
        }

        $remainsTemp = array();

        // переформатирую массив для следующей операции
        foreach ($remainsByStore as $productOnStore) {
            foreach ($productOnStore as $product) {
                foreach ($product as $item) {
                    $remainsTemp[] = array(
                        'idProduct' => $item->getIdProduct()->getId(),
                        'qty'     => $item->getQty(),
                        'price'   => $item->getPrice()
                    );
                    // добавляю в $data категории и производителей товаров, котроые есть в наличии
                    if (!in_array($item->getIdProduct()->getIdCatalog(), $data['catalog'])) {
                        $data['catalog'][] = $item->getIdProduct()->getIdCatalog();
                    }
                    if (!in_array($item->getIdProduct()->getIdBrand(), $data['brand'])) {
                        $data['brand'][] = $item->getIdProduct()->getIdBrand();
                    }
                }
            }
        }

        unset($remainsByStore);

        $remains = array();

        // суммирую количество товаров с одинаковой ценой
        foreach ($remainsTemp as $item) {
            if (!isset($remains[$item['idProduct']])) {
                $remains[$item['idProduct']] = array();
                $remains[$item['idProduct']]['product'] = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $item['idProduct']);
            }
            if (!isset($remains[$item['idProduct']][$item['price']])) {
                $remains[$item['idProduct']][$item['price']] = $item['qty'];
            } else {
                $qty = $remains[$item['idProduct']][$item['price']];
                $remains[$item['idProduct']][$item['price']] = $qty + $item['qty'];
            }
        }

        // оставляю в $data для отображения только те данные, которые присутствуют в записях Register
        if (count($allRegister) > 0) {
            foreach ($allRegister as $item) {
                if (!in_array($item->getIdStoreFrom(), $data['storeFrom'])) {
                    $data['storeFrom'][] = $item->getIdStoreFrom();
                }
//                if (!in_array($item->getIdStoreTo(), $data['storeTo'])) {
//                    $data['storeTo'][] = $item->getIdStoreTo();
//                }
                if (!in_array($item->getIdOperation(), $data['operation'])) {
                    $data['operation'][] = $item->getIdOperation();
                }
                if (!in_array($item->getIdPaymentType(), $data['paymentType'])) {
                    $data['paymentType'][] = $item->getIdPaymentType();
                }
                if (!in_array($item->getIdStatus(), $data['status'])) {
                    $data['status'][] = $item->getIdStatus();
                }
                if (!in_array($item->getIdUser(), $data['user'])) {
                    $data['user'][] = $item->getIdUser();
                }
            }
        }
        $data['storeTo'] = $myStore;

        return new ViewModel(array(
            'remains'   => $remains,
            'filter'    => $filter,
            'data'      => $data,
        ));
    }

    public function getDetailAction()
    {
        return new ViewModel();
    }

    /**
     * @return array|object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }

    /**
     * Get full category name with parent category
     * @param $id
     *
     * @return mixed
     */
    public function getFullNameCategory($id)
    {
        $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $id);
        $fullName = $category->getName();

        if (null == $category->getIdParent()) {
            if (!$this->fullName) {
                $this->fullName = $fullName;
            }

            return $this->fullName;
        } else {
            $parent = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $category->getIdParent());
            $parentName = $parent->getName();

            if ($this->fullName) {
                $this->fullName = $parentName . " :: " . $this->fullName;
            } else {
                $this->fullName = $parentName . " :: " . $fullName;
            }
        }

        return $this->getFullNameCategory($parent->getId());
    }
}

