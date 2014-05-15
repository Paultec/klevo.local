<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

class RegisterTableController extends AbstractActionController
{
    const REGISTER_ENTITY     = 'Register\Entity\Register';
    const PRODUCT_QTY_ENTITY  = 'Product\Entity\ProductCurrentQty';
    const PRODUCT_ENTITY      = 'Product\Entity\Product';
    const BRAND_ENTITY        = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY     = 'Catalog\Entity\Catalog';

    /**
     * @var
     */
    protected $em;

    protected $fullName;

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

        // Добавление введенных к-ва и цены в свойства выбранного товара
        $idProduct = $this->getRequest()->getPost('idProduct');
        if ($idProduct) {
            $currentProduct = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $idProduct);
            $currentProduct->currentQty   = $this->getRequest()->getPost('qty');
            $currentProduct->currentPrice = $this->getRequest()->getPost('price');

            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $currentProduct->getIdBrand());
            $currentProduct->brand = $brand->getName();

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $currentProduct->getIdCatalog());
            $currentProduct->category = $this->getFullNameCategory($category->getId());
        }

        //var_dump($currentProduct);
        //var_dump($currentSession->productList);
        if (isset($currentSession->productList)) {
            $currentSession->productList[] = $currentProduct;
        } elseif ($currentProduct) {
            $currentSession->productList = array();
            $currentSession->productList[] = $currentProduct;
        }
        //var_dump($currentSession->idRegister);

        $product = $this->forward()->dispatch('Product\Controller\Edit',
            array('action' => 'index', 'externalCall' => true));

        //Добавление свойства с текущим количеством товара
        if ($product->result) {
            for ($i = 0, $count = count($product->result); $i < $count; ++$i) {
                $idProduct = $product->result[$i]->getId();
                $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);

                $qtyProduct = $entityQtyProduct->getQty();
                $product->result[$i]->qty = $qtyProduct;
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

//        var_dump($currentSession->idRegister);
        $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $currentSession->idRegister);
        //$productList = $currentSession->productList;

//        var_dump($register);
        //var_dump($productList);

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
     * Get full name with parent category
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

}

