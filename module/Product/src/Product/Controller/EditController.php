<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        $type = $this->params('type', 'edit-product');
        $externalCall = $this->params('externalCall', false);

        // Получение queryString параметров (array)
        $param = $this->params()->fromQuery();

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
            'type'       => $type,
            'breadcrumbs'=> array('brand' => $brand, 'catalog' => $category),
            'result'     => $result
        ));

        if (!$externalCall) {
            $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));
            $res->addChild($catalog, 'catalog');
        }

        return $res;
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