<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Product\Entity\Product as ProductEntity;
use Product\Model\Product;

use GoSession;

class EditController extends AbstractActionController
{
    const PRODUCT_ENTITY  = 'Product\Entity\Product';
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';

    /**
     * @var
     */
    protected $em;

    public function indexAction()
    {
        $currentSession = new Container();

        // Если редирект на главную-редактирования через кнопку
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost('redirect');

            if (isset($data)) {
                unset($currentSession->idBrand);
                unset($currentSession->idCatalog);

                return $this->prg('/edit-product', true);
            }
        }

        $externalCall = $this->params('externalCall', false);

        // Получение queryString параметров (array)
        $param = $this->params()->fromQuery();

        // Сохранение/подстановка параметров запроса в/из сессии
        if (isset($currentSession->idBrand) && !isset($param['brand'])) {
            $param['brand'] = $currentSession->idBrand;
        } elseif (isset($param['brand'])) {
            $currentSession->idBrand = $param['brand'];
        }

        if (isset($currentSession->idCatalog) && !isset($param['catalog'])) {
            $param['catalog'] = $currentSession->idCatalog;
        } elseif (isset($param['catalog'])) {
            $currentSession->idCatalog = $param['catalog'];
        }

        // Формирование запроса, в зависимости от к-ва параметров
        if (isset($param['brand']) && isset($param['catalog'])) {
            $query = array(
                'idBrand'   => $param['brand'],
                'idCatalog' => $param['catalog']
            );

            $brand    = $this->getEntityManager()->find(self::BRAND_ENTITY, $param['brand']);
            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $param['catalog']);
        } elseif (isset($param['brand'])) {
            $query = array(
                'idBrand'   => $param['brand']
            );

            $brand    = $this->getEntityManager()->find(self::BRAND_ENTITY, $param['brand']);
        } elseif (isset($param['catalog'])) {
            $query = array(
                'idCatalog' => $param['catalog']
            );

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $param['catalog']);
        } else {
            $query = null;
        }

        if (!is_null($query)) {
            $result = $this->getEntityManager()
                ->getRepository(self::PRODUCT_ENTITY)->findBy($query);
        } else {
            $result = false;
        }

        $res = new ViewModel(array(
            'type'       => 'edit-product',
            'breadcrumbs'=> array('brand' => $brand, 'catalog' => $category),
            'result'     => $result
        ));

        if (!$externalCall) {
            $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));
            $res->addChild($catalog, 'catalog');
        }

        return $res;
    }

    public function addAction()
    {
        $form = $this->getForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = new ProductEntity();

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $postData = $form->getData();

                $postData['price'] = (int)($postData['price'] * 100);
                $postData['idCatalog'] = $this->getEntityManager()->
                    find(self::CATEGORY_ENTITY, $postData['idCatalog']);
                $postData['idBrand'] = $this->getEntityManager()->
                    find(self::BRAND_ENTITY, $postData['idBrand']);

                $product->populate($postData);

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('editproduct');
            }
        }

        return new ViewModel(array(
            'form'    => $form,
            'catalog' => $this->setOptionItems('catalog'),
            'brand'   => $this->setOptionItems('brand')
        ));
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('editproduct');
        }

        $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $id);

        $brandState   = $product->getIdBrand()->getId();
        $catalogState = $product->getIdCatalog()->getId();

        // Перевод в грн.
        $product->setPrice($product->getPrice() / 100);

        $form = $this->getForm();
        $form->setData($product->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $postData = $form->getData();

                $postData['idBrand'] = $this->getEntityManager()->
                    find(self::BRAND_ENTITY, $postData['idBrand']);
                $postData['idCatalog'] = $this->getEntityManager()->
                    find(self::CATEGORY_ENTITY, $postData['idCatalog']);
                // Обратно в коп.
                $postData['price'] = (int)($postData['price'] * 100);

                $product->populate($postData);

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                $this->redirect()->toRoute('editproduct');
            }
        }

        return new ViewModel(array(
            'form'         => $form,
            'catalog'      => $this->setOptionItems('catalog'),
            'brand'        => $this->setOptionItems('brand'),
            'brandState'   => $brandState,
            'catalogState' => $catalogState,
            'id'           => $id
        ));
    }

    /**
     * Set options for select
     *
     * @param $type
     *
     * @return array
     */
    protected function setOptionItems($type)
    {
        $option_arr = array();

        if ($type == 'catalog') {
            $categories = $this->getEntityManager()
                ->getRepository(self::CATEGORY_ENTITY)->findAll();

            for ($i = 0, $category = count($categories); $i < $category; $i++) {
                $option_arr[$i]['id']   = $categories[$i]->getId();
                $option_arr[$i]['name'] = $categories[$i]->getName();
            }
        } else {
            $brands = $this->getEntityManager()
                ->getRepository(self::BRAND_ENTITY)->findAll();

            for ($i = 0, $brand = count($brands); $i < $brand; $i++) {
                $option_arr[$i]['id']   = $brands[$i]->getId();
                $option_arr[$i]['name'] = $brands[$i]->getName();
            }
        }

        return $option_arr;
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Product();
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
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