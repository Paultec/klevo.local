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
    protected $fullName;
    private $currentSession;

    public function __construct()
    {
        $this->currentSession = new Container();
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        // Получение параметров из URL
        $routeParam = $this->params()->fromRoute();

//        if (!isset($this->currentSession->seoUrlParams)) {
//            $this->currentSession->seoUrlParams = array();
//        } else {
//            // Обнуление параметров сессии при удалении фильтра продуктов (breadcrumbs)
//            $this->clearSession($this->currentSession->seoUrlParams, $routeParam);
//        }

        //
        $param = $this->getFilterFromRouteParam(array(
                $routeParam['param1'],
                $routeParam['param2']
        ));
        //var_dump($param);
        if (!empty($param)) {
            $breadcrumbs = $this->setSessionByUrlParam($param);
        } else {
            $this->currentSession->seoUrlParams = array();
            $this->currentSession->flag = array();
            $breadcrumbs = array();
        }

        // Если найден несопоставимый параметр - вернуть 404
        if ($param === false) {
            $view = new ViewModel();
            $view->setTemplate('error/404');

            return $view;
        }

        // Формирование запроса, в зависимости от к-ва параметров
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb->select('p')
            ->from(self::PRODUCT_ENTITY, 'p')
            ->where('p.price != 0')
            ->andWhere(
                $qb->expr()->orX('p.idStatus != 4', 'p.idStatus IS NULL')
            );

        //$breadcrumbs = array();
        if (!empty($param)) {
            $count = 1;
            foreach ($param as $key => $value) {
                $qb->andWhere('p.id' . ucfirst($key) . ' = ?' . $count)
                    ->setParameter($count, $value);

                // Получаем крошки
//                if ($key != 'brand') {
//                    $breadcrumbs['catalog']['id']       = $value;
//                    $breadcrumbs['catalog']['translit'] = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $value)->getTranslit();
//                    $breadcrumbs['catalog']['name']     = $this->getFullNameCategory($value);
//                    // seoUrlParams['idCatalog'] нужен в Catalog/IndexController
//                    // для связанного отображения категорий и производителей
//                    $this->currentSession->seoUrlParams['idCatalog'] = $value;
//                } else {
//                    $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $value);
//
//                    $breadcrumbs['brand']['id']       = $brand->getId();
//                    $breadcrumbs['brand']['translit'] = $brand->getTranslit();
//                    $breadcrumbs['brand']['name']     = 'Производитель :: ' . $brand->getName();
//                    // seoUrlParams['idBrand'] нужен в Catalog/IndexController
//                    // для связанного отображения категорий и производителей
//                    $this->currentSession->seoUrlParams['idBrand'] = $brand->getId();
//                }

                $count++;
            }
        } else {
//            unset($this->currentSession->seoUrlParams['idCatalog']);
//            unset($this->currentSession->seoUrlParams['idBrand']);
        }

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qs));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(24);

        $res = new ViewModel(array(
            'paginator'    => $paginator,
            'seoUrlParams' => $this->currentSession->seoUrlParams,
            'breadcrumbs'  => $breadcrumbs ?: null,
        ));
        //var_dump($breadcrumbs);
        //var_dump($this->currentSession->seoUrlParams);
        //var_dump($this->currentSession->flag);

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $name = (string)$this->params()->fromRoute('name', null);

        if (!$name) {
            return $this->redirect()->toRoute('product');
        }

        $product = $this->getEntityManager()
            ->getRepository(self::PRODUCT_ENTITY)->findOneBy(array('translit' => $name));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res = new ViewModel(array(
            'product' => $product
        ));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    /**
     * @param $sessionParam
     * @param $routeParam
     */
    protected function clearSession($sessionParam, $routeParam)
    {
//        for ($i = 1, $count = count($sessionParam); $i < $count; $i++) {
//            $currentParam = $sessionParam['param' . $i];
//
//            if (!is_null($currentParam) && !in_array($currentParam, $routeParam)) {
//                unset($this->currentSession->seoUrlParams['param' . $i]);
//
//                $attributes = $this->getAttributesParams();
//                foreach ($attributes as $attributeKey => $attributeValue) {
//                    if (isset($attributeValue[$currentParam])) {
//                        unset($this->currentSession->seoUrlParams['id' . ucfirst($attributeKey)]);
//                    }
//                }
//            }
//        }
//        // fix: Переместить param2 в param1, если param1 отсутствует
//        if (isset($this->currentSession->seoUrlParams['param2'])
//            && !$this->currentSession->seoUrlParams['param1']) {
//            $this->currentSession->seoUrlParams['param1'] = $this->currentSession->seoUrlParams['param2'];
//            unset($this->currentSession->seoUrlParams['param2']);
//        }
    }

    /**
     * @param $param
     *
     * @return array
     */
    protected function getFilterFromRouteParam($param)
    {
        $result = array();

        $ent    = array(
            'brand'   => self::BRAND_ENTITY,
            'catalog' => self::CATEGORY_ENTITY
        );

        $attributes = $this->getAttributesParams();

        $this->currentSession->flag = array();

        // Проверка на несуществующий параметр
        $is404 = array();
        $countParam = 0;

        $count = 1;
        foreach ($param as $item) {
            // Пропустить значение элемента поумолчанию (пустая строка)
            if (empty($item)) {
                $is404[$countParam] = false;

                continue;
            }

            $is404[$countParam] = true;

            foreach ($attributes as $key => $value) {
                if (isset($value[$item])) {
                    $is404[$countParam] = false;

                    $element = $this->getEntityManager()->getRepository($ent[$key])
                        ->findOneBy(array('translit' => $param));

                    $result[$key] = $element->getId();

                    $this->currentSession->flag[$key] = true;
                    $this->currentSession->seoUrlParams['param'.$count] = $element->getTranslit();

                    $count++;

                    break;
                }
            }

            $countParam++;
        }

        foreach ($is404 as $page404) {
            if ($page404) {
                // Найдено несопоставимое значение
                return false;
            }
        }

        return $result;
    }

    /**
     * @return array|mixed
     */
    private function getAttributesParams()
    {
        $result = array();

        $cache = $this->getServiceLocator()->get('filesystem');

        if (!$cache->hasItem('params')) {
            $brand    = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)->findAll();
            $category = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)->findAll();

            foreach ($brand as $brandItem) {
                $result['brand'][$brandItem->getTranslit()] = true;
            }

            foreach ($category as $categoryItem) {
                $result['catalog'][$categoryItem->getTranslit()] = true;
            }

            $cache->setItem('params', serialize($result));
        } else {
            $result = unserialize($cache->getItem('params'));
        }

        return $result;
    }

    /**
     * Get full category name with parent category
     *
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

    /**
     *
     */
    protected function setSessionByUrlParam($urlParam)
    {
        $this->currentSession->seoUrlParams = array();
        $this->currentSession->flag = array();
        $breadcrumbs = array();
        $countParam = 1;

        if (isset($urlParam['brand'])) {
            $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $urlParam['brand']);
            $this->currentSession->seoUrlParams['param' . $countParam] = $brand->getTranslit();
            $this->currentSession->seoUrlParams['idBrand'] = $urlParam['brand'];
            $breadcrumbs['brand']['id'] = $brand->getId();
            $breadcrumbs['brand']['translit'] = $brand->getTranslit();
            $breadcrumbs['brand']['name'] = 'Производитель :: ' . $brand->getName();
            $this->currentSession->flag['brand'] = true;
            ++$countParam;
        }

        if (isset($urlParam['catalog'])) {
            $catalog = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $urlParam['catalog']);
            $this->currentSession->seoUrlParams['param' . $countParam] = $catalog->getTranslit();
            $this->currentSession->seoUrlParams['idCatalog'] = $urlParam['catalog'];
            $breadcrumbs['catalog']['id'] = $catalog->getId();
            $breadcrumbs['catalog']['translit'] = $catalog->getTranslit();
            $breadcrumbs['catalog']['name'] = $this->getFullNameCategory($urlParam['catalog']);
            $this->currentSession->flag['catalog'] = true;
            ++$countParam;
        }

        return $breadcrumbs;
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