<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

class RegisterTableController extends AbstractActionController
{
    const REGISTER_ENTITY  = 'Register\Entity\Register';
    const PRODUCT_QTY_ENTITY  = 'Product\Entity\ProductCurrentQty';

    /**
     * @var
     */
    protected $em;

    public function indexAction()
    {
        //Сохранение и получение текущего id записи из таблицы Register
        $currentSession = new Container();
        if (isset($currentSession->idRegister)) {
            $idRegister = $currentSession->idRegister;
        } else {
            $currentSession->idRegister = $this->params('content');
            $idRegister = $currentSession->idRegister;
        }

        $register = $this->getEntityManager()->find(self::REGISTER_ENTITY, $idRegister);

        $post = $this->getRequest()->getPost();
        var_dump($post);
        $post = $this->getRequest()->getPost('idRegister');
        var_dump($post);

        $product = $this->forward()->dispatch('Product\Controller\Edit',
            array('action' => 'index', 'externalCall' => true));

        //Добавление свойства с текущим количеством товара
        if ($product->result) {
            $count = count($product->result);
            for ($i=0; $i<$count; ++$i) {
                $idProduct = $product->result[$i]->getId();
                $entityQtyProduct = $this->getEntityManager()->find(self::PRODUCT_QTY_ENTITY, $idProduct);
                $qtyProduct = $entityQtyProduct->getQty();
                $product->result[$i]->qty = $qtyProduct;
            }
        }

        //var_dump($product->result);

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res =  new ViewModel(array(
            'register' => $register,
            'product'    => $product,
            'type'       => 'register-table'
        ));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    public function addAction()
    {
        $currentSession = new Container();
        unset($currentSession->idBrand);
        unset($currentSession->idCatalog);
        unset($currentSession->idRegister);
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

}

