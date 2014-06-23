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

            $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $idRegister);

            $idProduct = $this->getRequest()->getPost('idProduct');
            if ($idProduct) {
                $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);

                // Добавление введенных к-ва и цены в свойства выбранного товара
                $currentProduct->currentQty   = $this->getRequest()->getPost('qty');
                $currentProduct->currentPrice = $this->getRequest()->getPost('price');

                // Добавление idRegister, idOperation, idUser текущей операции к свойствам,
                // чтобы не делать дополнительные запросы в addAction
                //$currentProduct->currentIdRegister  = $register->getId();
                //$currentProduct->currentIdUser      = $register->getIdUser();
                //$currentProduct->currentIdOperation = $register->getIdOperation();

                $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $currentProduct->getIdBrand());
                $currentProduct->brand = $brand->getName();

                $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $currentProduct->getIdCatalog());
                $currentProduct->category = $this->getFullNameCategory($category->getId());
            }

            // Формирование массива с выбранными товарами
            if (isset($currentSession->productList)) {
                $currentSession->productList[] = $currentProduct;
            } elseif ($currentProduct) {
                $currentSession->productList = array();
                $currentSession->productList[] = $currentProduct;
            }

            $product = $this->forward()->dispatch('Product\Controller\Edit',
                array('action' => 'index', 'externalCall' => true));

            //Добавление свойства с текущим количеством товара
            if ($product->result) {
                for ($i = 0, $count = count($product->result); $i < $count; ++$i) {
                    $idProduct = $product->result[$i]->getId();
                    $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);

                    $qtyProduct = $entityQtyProduct->getQty();
                    $product->result[$i]->setQty($qtyProduct);
                }
            }

            $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

            $res =  new ViewModel(array(
                'register'    => $register,
                'product'     => $product,
                'type'        => 'register-table',
                'productList' => $currentSession->productList,
            ));

            $res->addChild($catalog, 'catalog');

            return $res;
    }

    public function addAction()
    {
        $currentSession = new Container();

                $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $currentSession->idRegister);

                $registerTable = array();

                $productList = $currentSession->productList;

                $count = count($productList);
                for ($i=0; $i<$count; ++$i) {
                    //var_dump($productList[$i]);
                    $idProduct = $productList[$i]->getId();
                    $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);
                    $qty = $productList[$i]->currentQty;
                    unset($productList[$i]->currentQty);
                    $price = $productList[$i]->currentPrice;
                    unset($productList[$i]->currentPrice);
                    //$idUser = $productList[$i]->currentIdUser;
                    //var_dump($idUser);
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
            }

            return $this->getFullNameCategory($parent->getId());
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


}

