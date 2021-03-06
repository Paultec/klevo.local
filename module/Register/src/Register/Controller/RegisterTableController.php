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
    const ORDER_TABLE_ENTITY    = 'Register\Entity\OrderTable';

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
        $param = $this->params('content');

        //Сохранение и получение текущего id записи из таблицы Register
        if (isset($currentSession->idRegister)) {
            $idRegister = $currentSession->idRegister;
        } else {
            $currentSession->idRegister = $param[0];
            $idRegister = $currentSession->idRegister;
        }

        if (!is_null($param[1])) {
            $currentProductArr = array();
            $orderTable = $this->getEntityManager()->getRepository(self::ORDER_TABLE_ENTITY)->findBy(array('idOrder' => $param[1]));

            $currentSession->productList = array();
            foreach ($orderTable as $orderTableItem) {
                $currentProduct = $orderTableItem->getIdProduct();

                $currentProductArr['id'] = $currentProduct->getId();
                $currentProductArr['name'] = $currentProduct->getName();

                // Добавление введенных к-ва и цены в свойства выбранного товара
                $currentProductArr['qty']   = $orderTableItem->getQty();
                $currentProductArr['price'] = $orderTableItem->getPrice() / 100;

                $brand = $currentProduct->getIdBrand();
                $currentProductArr['brand'] = $brand->getName();

                $category = $currentProduct->getIdCatalog();
                $currentProductArr['category'] = $this->fullNameService->getFullNameCategory($category->getId());
                $this->fullNameService->setFullNameToNull();

                $currentSession->productList[] = $currentProductArr;
            }
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
            $currentProductArr = array();

            $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);

            $currentProductArr['id']    = $currentProduct->getId();
            $currentProductArr['name']  = $currentProduct->getName();

            // Добавление введенных к-ва и цены в свойства выбранного товара
            $currentProductArr['qty']   = (int)$this->getRequest()->getPost('qty');
            $currentProductArr['price'] = $this->getRequest()->getPost('price');

            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $currentProduct->getIdBrand());
            $currentProductArr['brand'] = $brand->getName();

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $currentProduct->getIdCatalog());
            $currentProductArr['category'] = $this->fullNameService->getFullNameCategory($category->getId());
        }

        // Формирование массива с выбранными товарами
        if (isset($currentSession->productList) && $currentProductArr && is_null($param[1])) {
            $currentSession->productList[] = $currentProductArr;
        } elseif ($currentProductArr && is_null($param[1])) {
            $currentSession->productList = array();
            $currentSession->productList[] = $currentProductArr;
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

        $res = new ViewModel(array(
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

        for ($i = 0, $count = count($productList); $i < $count; $i++) {
            $idProduct  = $productList[$i]['id'];
            $product    = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);
            $qty        = $productList[$i]['qty'];
            $price      = $productList[$i]['price'] * 100;
            $totalSum  += $qty * $price;

            unset($productList[$i]['qty']);
            unset($productList[$i]['price']);

            $idUser = $register->getIdUser();
            $user   = $this->getEntityManager()->find(self::USER_ENTITY, $idUser);

            $idOperation = $register->getIdOperation();
            $operation   = $this->getEntityManager()->find(self::OPERATION_ENTITY, $idOperation);

            unset($productList[$i]['brand']);
            unset($productList[$i]['category']);

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
            $registerTableNote = new RegisterTableEntity();

            $registerTableNote->populate($item);

            $this->getEntityManager()->persist($registerTableNote);
            $this->getEntityManager()->flush();
        }

        $register->setTotalSum($totalSum);
        $this->getEntityManager()->persist($register);
        $this->getEntityManager()->flush();

        unset($currentSession->idBrand);
        unset($currentSession->idCatalog);
        unset($currentSession->idRegister);
        unset($currentSession->productList);

        return $this->redirect()->toRoute('register');
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
     * @return mixed
     */
    public function removeSessionProductAction()
    {
        $currentSession = new Container();

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest() || !empty($currentSession->productList)) {
            $postData = $request->getPost('idProduct');

            $currentProductList = $currentSession->productList;
            unset($currentSession->productList);

            $newProductList = array();
            foreach ($currentProductList as $productItem) {
                if ($productItem['id'] != $postData) {
                    $newProductList[] = $productItem;
                }
            }

            $currentSession->productList = $newProductList;
        }

        return $this->redirect()->toRoute('admin');
    }

    public function editSessionProductAction()
    {
        $currentSession = new Container();

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest() || !empty($currentSession->productList)) {
            $postData = (array)$request->getPost();

            $currentProductList = $currentSession->productList;
            unset($currentSession->productList);

            $newProductList = array();
            foreach ($currentProductList as $productItem) {
                if ($productItem['id'] != $postData['idProduct']) {
                    $newProductList[] = $productItem;
                } else {
                    $tmp = array();

                    $tmp['id']       = $productItem['id'];
                    $tmp['name']     = $productItem['name'];
                    $tmp['qty']      = $postData['qty'];
                    $tmp['price']    = $postData['price'];
                    $tmp['brand']    = $productItem['brand'];
                    $tmp['category'] = $productItem['category'];

                    $newProductList[] = $tmp;
                }
            }

            $currentSession->productList = $newProductList;
        }

        return $this->redirect()->toRoute('admin');
    }

    /**
     * @return \Zend\Http\Response
     */
    public function clearAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $currentSession = new Container();

            $registerTable = $this->getEntityManager()->getRepository(self::REGISTER_TABLE_ENTITY)->findBy(array('idRegister' => $currentSession->idRegister));

            if (empty($registerTable)) {
                $register = $this->getEntityManager()->getRepository(self::REGISTER_ENTITY)->findBy(array('id' => $currentSession->idRegister));

                foreach ($register as $registerItem) {
                    $this->getEntityManager()->remove($registerItem);
                }

                $this->getEntityManager()->flush();
            }
        }

        return $this->redirect()->toRoute('register');
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