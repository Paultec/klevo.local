<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;

use GoSession;

use Register\Entity\RegisterTable as RegisterTableEntity;

class RegisterTableController extends AbstractActionController
{
    const OPERATION_ENTITY      = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY   = 'Data\Entity\PaymentType';
    const STATUS_ENTITY         = 'Data\Entity\Status';
    const STORE_ENTITY          = 'Data\Entity\Store';
    const USER_ENTITY           = 'User\Entity\User';
    const REGISTER_ENTITY       = 'Register\Entity\Register';
    const REGISTER_TABLE_ENTITY = 'Register\Entity\RegisterTable';
    const PRODUCT_ENTITY        = 'Product\Entity\Product';
    const PRODUCT_QTY_ENTITY    = 'Product\Entity\ProductCurrentQty';
    const BRAND_ENTITY          = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY       = 'Catalog\Entity\Catalog';

    /**
     * @var
     */
    protected $em = null;
    private $cache;
    private $fullNameService;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();

        $currentSession = new Container();

        //Сохранение и получение текущего id записи из таблицы Register
        if (isset($currentSession->idRegister)) {
            $idRegister = $currentSession->idRegister;
        } else {
            $currentSession->idRegister = $this->params('content');
            $idRegister = $currentSession->idRegister;
        }

        $qs = null;

        $filter = array();

        // в этом кэше хранятся брэнд и/или категория товаров, выбранные пользователем
        $filterCache = unserialize($this->cache->getItem($currentUser));

        // если этого кэша нет, создаем его (с пустым массивом)
        if (!$filterCache) {
            $filterCache = $filter;
            $this->cache->addItem($currentUser, serialize($filterCache));
        } else {
            $filter = $filterCache;
        }

        $request = $this->getRequest();
        // здесь записываем в $filter выбранные пользователем брэнд и/или категорию
        if ($request->isPost()) {
            if ($request->getPost('idBrand') || $request->getPost('idCatalog')) {
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qs = $qb->select('p')->from(self::PRODUCT_ENTITY, 'p');

                if ($request->getPost('idBrand')) {
                    $filter['idBrand'] = $request->getPost('idBrand');
                    $qb->andWhere('p.idBrand = :idBrand')->setParameter('idBrand', $filter['idBrand']);
                }

                if ($request->getPost('idCatalog')) {
                    $filter['idCatalog'] = $request->getPost('idCatalog');
                    $qb->andWhere('p.idCatalog = :idCatalog')->setParameter('idCatalog', $filter['idCatalog']);
                }
            } else {
                $this->cache->removeItem($currentUser);
            }
        }

        $this->cache->replaceItem($currentUser, serialize($filter));

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
            $currentProduct->category = $this->fullNameService->getFullNameCategory($category->getId());
        }

        // Формирование массива с выбранными товарами
        if (isset($currentSession->productList) && $currentProduct) {
            $currentSession->productList[] = $currentProduct;
        } elseif ($currentProduct) {
            $currentSession->productList = array();
            $currentSession->productList[] = $currentProduct;
        }

        //Добавление свойства с текущим количеством товара
        if ($product->result) {
            for ($i = 0, $count = count($product->result); $i < $count; ++$i) {
                $idProduct = $product->result[$i]->getId();
                $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);

                $qtyProduct = $entityQtyProduct->getQty();
                $product->result[$i]->setQty($qtyProduct);
            }
        }

        $res =  new ViewModel(array(
            'register'    => $register,
            'catalogList' => $catalogList,
            'product'     => $product,
            'productList' => $currentSession->productList,
        ));

        return $res;
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $currentSession = new Container();

        $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $currentSession->idRegister);

        $registerTable = array();

        $productList = $currentSession->productList;

        $totalSum = 0;

        $count = count($productList);
        for ($i = 0; $i < $count; ++$i) {
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

        return $this->redirect()->toRoute('register');

//        return new ViewModel();
    }

    /**
     * @return ViewModel
     */
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
     * Set cache from factory
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * Set fullName service
     *
     * @param $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullNameService = $fullName;
    }

    /**
     * @return array|mixed
     */
    private function getCatalogList()
    {
        $result = array();

        $this->cache->removeItem('catalogList');

        if (!$this->cache->hasItem('catalogList')) {
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
                            'name' => $this->fullNameService->getFullNameCategory($categoryItem->getId()),
                        )
                    );
                }

                $this->fullNameService->setFullNameToNull();
            }

            $count = 1;
            foreach ($brand as $brandItem) {
                $result['brand'][$count]['id'] = $brandItem->getId();
                $result['brand'][$count]['name'] = $brandItem->getName();
                ++$count;
            }

            $this->cache->setItem('catalogList', serialize($result));
        } else {
            $result = unserialize($this->cache->getItem('catalogList'));
        }

        return $result;
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
}