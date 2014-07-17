<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Session\Container;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use GoSession;

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
        $param = array();

        $routeParam = $this->params()->fromRoute();
        var_dump($routeParam);

        $currentSession = new Container();
        //var_dump($currentSession->seoUrlParams);
        if (!isset($currentSession->seoUrlParams)) {
            $currentSession->seoUrlParams = array();
        }
//        var_dump($currentSession->seoUrlParams);

        if (isset($routeParam['brand'])) {
            $currentSession->seoUrlParams['brandName'] = $routeParam['brand'];
        }

        if (isset($routeParam['category'])) {
            $currentSession->seoUrlParams['categoryName'] = $routeParam['category'];
        }

        if (isset($routeParam['page'])) {
            $currentSession->seoUrlParams['pageNum'] = $routeParam['page'];
        }
        var_dump($currentSession->seoUrlParams);

//        var_dump($routeParam);

        // URL сопоставление транслитерации с id
        if (isset($routeParam['brand'])) {
            $brand = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)
                ->findBy(array('translit' => $routeParam['brand']));

            $param['brand'] = $brand[0]->getId();
        }

        if (isset($routeParam['category'])) {
            $category = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)
                ->findBy(array('translit' => $routeParam['category']));

            $param['catalog'] = $category[0]->getId();
        }

        // Получение queryString параметров (array)
//        $param = $this->params()->fromQuery();

        // Формирование запроса, в зависимости от к-ва параметров
        if (!empty($param['brand']) && !empty($param['catalog'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idBrand = ' . $param['brand'] .
                ' AND p.idCatalog = ' . $param['catalog'] .
                ' AND p.price != 0 AND (p.idStatus != 4 OR p.idStatus IS NULL)'
            );

            $brand    = $this->getBreadcrumbs($param, 'brand');
            $category = $this->getBreadcrumbs($param, 'catalog');
        } elseif (!empty($param['brand'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idBrand = ' . $param['brand'] .
                ' AND p.price != 0 AND (p.idStatus != 4 OR p.idStatus IS NULL)'
            );

            $brand = $this->getBreadcrumbs($param, 'brand');
        } elseif (!empty($param['catalog'])) {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.idCatalog = ' . $param['catalog'] .
                ' AND p.price != 0 AND (p.idStatus != 4 OR p.idStatus IS NULL)'
            );

            $category = $this->getBreadcrumbs($param, 'catalog');
        } else {
            $dql = $this->getEntityManager()->createQuery(
                'SELECT p FROM Product\Entity\Product p
                WHERE p.price != 0 AND (p.idStatus != 4 OR p.idStatus IS NULL)'
            );
        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($dql));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(24);

        $res = new ViewModel(array(
            'paginator'  => $paginator,
            'breadcrumbs'=> array('brand' => $brand, 'catalog' => $category),
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    public function brandAction()
    {
        var_dump('brandAction'); exit;
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('product');
        }

        $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $id);

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res = new ViewModel(array(
            'product' => $product
        ));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    /**
     * @param $param
     * @param $type
     *
     * @return array
     */
    protected function getBreadcrumbs($param, $type)
    {
        if ($type == 'brand') {
            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $param['brand']);

            $arr = array(
                'Производитель :: ',
                $brand
            );
        } else {
            $first  = null;
            $parent = null;

            $catalog = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $param['catalog']);

            if (!is_null($catalog->getIdParent()) && !is_null($catalog->getIdParent()->getId())) {
                $parent = $this->getEntityManager()
                    ->getRepository(self::CATEGORY_ENTITY)->findBy(array('id' => $catalog->getIdParent()->getId()));

                if (!is_null($parent[0]->getIdParent())) {
                    $first = $this->getEntityManager()
                        ->getRepository(self::CATEGORY_ENTITY)->findBy(array('id' => $parent[0]->getIdParent()->getId()));
                }
            }

            $arr = array(
                $first,
                $parent,
                $catalog
            );
        }

        return $arr;
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