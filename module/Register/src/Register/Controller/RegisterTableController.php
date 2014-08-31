<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

use Register\Entity\RegisterTable as RegisterTableEntity;

class RegisterTableController extends AbstractActionController
{
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
    /**
     * @var
     */
    protected $em = null;

    protected $fullName = null;

    public function indexAction()
    {
        $currentSession = new Container();

        //Сохранение и получение текущего id записи из таблицы Register
        if (isset($currentSession->idRegister)) {
            $idRegister = $currentSession->idRegister;
        } else {
            $currentSession->idRegister = $this->params('content');
            $idRegister = $currentSession->idRegister;
        }

        $request = $this->getRequest();

        $qs = null;

        if ($request->isPost()) {
            if ($request->getPost('idBrand') || $request->getPost('idCatalog')) {
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qs = $qb->select('p')->from(self::PRODUCT_ENTITY, 'p');

                if ($request->getPost('idBrand')) {
                    $currentSession->register['idBrand'] = $request->getPost('idBrand');
                    $qb->andWhere('p.idBrand = :idBrand')->setParameter('idBrand', $currentSession->register['idBrand']);
                }

                if ($request->getPost('idCatalog')) {
                    $currentSession->register['idCatalog'] = $request->getPost('idCatalog');
                    $qb->andWhere('p.idCatalog = :idCatalog')->setParameter('idCatalog', $currentSession->register['idCatalog']);
                }
            }
        }

        $product = !is_null($qs) ? $qs->getQuery()->getResult() : $qs;

        $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $idRegister);

        $catalogList = $this->getCatalogList();

        $idProduct = $this->getRequest()->getPost('idProduct');
        if ($idProduct) {
            $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);

            // Добавление введенных к-ва и цены в свойства выбранного товара
            $currentProduct->currentQty   = $this->getRequest()->getPost('qty');
            $currentProduct->currentPrice = $this->getRequest()->getPost('price');

            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $currentProduct->getIdBrand());
            $currentProduct->brand = $brand->getName();

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $currentProduct->getIdCatalog());
            $currentProduct->category = $this->getFullNameCategory($category->getId());
        }

        // Формирование массива с выбранными товарами
//        if (isset($currentSession->productList)) {
//            $currentSession->productList[] = $currentProduct;
//        } elseif ($currentProduct) {
//            $currentSession->productList = array();
//            $currentSession->productList[] = $currentProduct;
//        }

//        $product = $this->forward()->dispatch('Product\Controller\Edit',
//            array('action' => 'index', 'externalCall' => true));

        //Добавление свойства с текущим количеством товара
//        if ($product->result) {
//            for ($i = 0, $count = count($product->result); $i < $count; ++$i) {
//                $idProduct = $product->result[$i]->getId();
//                $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);
//
//                $qtyProduct = $entityQtyProduct->getQty();
//                $product->result[$i]->setQty($qtyProduct);
//            }
//        }

//        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res =  new ViewModel(array(
            'register'      => $register,
            'catalogList'   => $catalogList,
            'product'     => $product,
            //'type'        => 'register-table',
            //'productList' => $currentSession->productList,
        ));

//        $res->addChild($catalog, 'catalog');

        return $res;
    }

    public function addAction()
    {
        $currentSession = new Container();

                $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $currentSession->idRegister);

                $registerTable = array();

                $productList = $currentSession->productList;

                $totalSum = 0;

                $count = count($productList);
                for ($i=0; $i<$count; ++$i) {
                    //var_dump($productList[$i]);
                    $idProduct = $productList[$i]->getId();
                    $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);
                    $qty = $productList[$i]->currentQty;
                    $price = $productList[$i]->currentPrice;
                    $totalSum += $qty * $price;
                    unset($productList[$i]->currentQty);
                    unset($productList[$i]->currentPrice);
                    //$idUser = $productList[$i]->currentIdUser;
                    $idUser = $register->getIdUser();
                    $user = $this->getEntityManager()->find(self::USER_ENTITY, $idUser);
                    //unset($productList[$i]->currentIdUser);
                    //$idOperation = $productList[$i]->currentIdOperation;
                    //var_dump($idOperation);
                    $idOperation = $register->getIdOperation();
                    $operation = $this->getEntityManager()->find(self::OPERATION_ENTITY, $idOperation);
                    //unset($productList[$i]->currentIdOperation);
                    unset($productList[$i]->brand);
                    //unset($productList[$i]->qty);
                    unset($productList[$i]->category);
                    //var_dump($productList[$i]);
                    $noteRegisterTable = array(
                        'idProduct'   => $product,
                        'qty'         => $qty,
                        'price'       => $price,
                        'idRegister'  => $register,
                        'idUser'      => $user,
                        'idOperation' => $operation
                    );
                    $registerTable[] = $noteRegisterTable;
                }

                foreach ($registerTable as $item) {
        //            var_dump($item);
                    $registerTableNote = new RegisterTableEntity();

                    $registerTableNote->populate($item);
                    //var_dump($registerTableNote);

                    $this->getEntityManager()->persist($registerTableNote);
                    $this->getEntityManager()->flush();
                }

                //var_dump($totalSum);
                //var_dump($register);
                $register->setTotalSum($totalSum);
                //var_dump($register);
        $this->getEntityManager()->persist($register);
        $this->getEntityManager()->flush();

                unset($currentSession->idBrand);
                unset($currentSession->idCatalog);
                unset($currentSession->idRegister);
                unset($currentSession->productList);

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

            return $this->getFullNameCategory($parent->getId());
        }
    }

    public function getDetailAction()
    {
        $idRegister = $this->getRequest()->getPost('idRegister');
        $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $idRegister);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->add('select', 'rt')
           ->add('from', 'Register\Entity\RegisterTable rt')
           ->where('rt.idRegister = :idRegister')
           ->setParameter('idRegister', $register->getId());
        $query = $qb->getQuery();
        $registerTable = $query->getResult();

        return new ViewModel(array(
            'register'      => $register,
            'registerTable' => $registerTable
        ));
    }

    /**
     * @return array|mixed
     */
    private function getCatalogList()
    {
        $result = array();

        $cache = $this->getServiceLocator()->get('filesystem');
        $cache->removeItem('catalogList');

        if (!$cache->hasItem('catalogList')) {
            $brand    = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)->findAll();
            $categories = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)->findAll();

            foreach ($categories as $categoryItem) {
                if (!is_null($categoryItem->getIdParent())) {
                    $mainCategory[] = $categoryItem->getIdParent()->getId();
                }
            }

            $mainCategory = array_unique($mainCategory);

            $result['category'] = array();
            $result['category'][0]['id'] = null;
            $result['category'][0]['name'] = 'Выбрать категорию';
            $result['brand'] = array();
            $result['brand'][0]['id'] = null;
            $result['brand'][0]['name'] = 'Выбрать производителя';

            foreach ($categories as $categoryItem) {
                if (!in_array($categoryItem->getId(), $mainCategory)) {
                    // Для того чтобы выставить ключи массива по-порядку
                    // потому как array_unique сохраняет значение ключей
                    // используем array_push
                    array_push(
                        $result['category'],
                        array(
                            'id'   => $categoryItem->getId(),
                            'name' => $this->getFullNameCategory($categoryItem->getId()),
                        )
                    );
                }
                $this->fullName = null;
            }

            $count = 1;
            foreach ($brand as $brandItem) {
                $result['brand'][$count]['id'] = $brandItem->getId();
                $result['brand'][$count]['name'] = $brandItem->getName();
                ++$count;
            }

            $cache->setItem('catalogList', serialize($result));
        } else {
            $result = unserialize($cache->getItem('catalogList'));
        }

        return $result;
    }
}

