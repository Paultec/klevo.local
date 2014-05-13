<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY  = 'Product\Entity\Product';
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';

    /**
     * @var
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        // Получение queryString параметров (array)
        $param = $this->params()->fromQuery();

        // Формирование запроса, в зависимости от к-ва параметров
        if (isset($param['brand']) && isset($param['catalog'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idBrand = ' . $param['brand'] .
                ' AND p.idCatalog = ' . $param['catalog']
            );

            $brand    = $this->getEntityManager()->find(self::BRAND_ENTITY, $param['brand']);
            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $param['catalog']);
        } elseif (isset($param['brand'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idBrand = ' . $param['brand']
            );

            $brand    = $this->getEntityManager()->find(self::BRAND_ENTITY, $param['brand']);
        } elseif (isset($param['catalog'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idCatalog = ' . $param['catalog']
            );

            $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $param['catalog']);
        } else {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p'
            );
        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($dql));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(12);

        $res = new ViewModel(array(
            'paginator'  => $paginator,
            'breadcrumbs'=> array('brand' => $brand, 'catalog' => $category),
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res->addChild($catalog, 'catalog');

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