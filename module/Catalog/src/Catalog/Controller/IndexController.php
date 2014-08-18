<?php
namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

class IndexController extends AbstractActionController
{
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';
    const PRODUCT_ENTITY  = 'Product\Entity\Product';

    /**
     * @var null|object
     */
    protected $em;

    /**
     * @var array
     */
    protected $actualCatalogList;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        // Передать имя роута на который должны переходить ссылки
        // При вызове через dispatch 'route' => 'route/subRoute'
        $route = $this->params('route', 'product/seoUrl');

        $currentSession = new Container();

        if (isset($currentSession->seoUrlParams)) {
            $seoUrlParams = $currentSession->seoUrlParams;
        } else {
            $seoUrlParams = array();
        }

        if (isset($seoUrlParams['idBrand'])) {
            $categoryList = $this->getActualCatalog($seoUrlParams['idBrand']);
        } else {
            $categoryList = $this->getLoop(self::CATEGORY_ENTITY);
        }

        if (isset($seoUrlParams['idCatalog'])) {
            $brandList = $this->getActualBrand($seoUrlParams['idCatalog']);
        } else {
            $brandList = $this->getLoop(self::BRAND_ENTITY);
        }

        return new ViewModel(array(
            'brandList'    => $brandList,
            'categoryList' => $categoryList,
            'seoUrlParams' => $seoUrlParams,
            'sessionFlag'  => $currentSession->flag,
            'route'        => $route
        ));
    }

    /**
     * @param $idBrand
     *
     * @return array
     */
    protected function getActualCatalog($idBrand)
    {
        $em = $this->getEntityManager();

        // получаем список продуктов выбранного бренда
        $query = $em->createQuery('SELECT p FROM Product\Entity\Product p WHERE p.idBrand = :idBrand')
            ->setParameter('idBrand', $idBrand);

        $listProduct = $query->getResult();

        $listIdCatalog = array();

        // получаем список уникальных ID категорий, встречающихся в отобранном списке товаров
        // ВАЖНО: не делаем $listIdCatalog[] = $item->getIdCatalog();
        // потому что в этом случае не срабатывает array_unique
        foreach ($listProduct as $item) {
            $listIdCatalog[] = $item->getIdCatalog()->getId();
            $listIdCatalog = array_unique($listIdCatalog);
        }

        $fullCatalogList = $this->getLoop(self::CATEGORY_ENTITY);

        $categoryList = array();

        // формируем массив встречающихся категорий
        foreach ($fullCatalogList as $catalog) {
            if (in_array($catalog['id'], $listIdCatalog)) {
                $categoryList[] = $catalog;
            }
        }

        $this->actualCatalogList = array();

        // для каждой категории получаем родительские категории
        foreach ($categoryList as $category) {
            $this->getParentCategory($category);
        }

        $actualCatalogList = array();

        // убираем дубликаты из массива категорий
        // array_unique НЕ РАБОТАЕТ
        foreach ($this->actualCatalogList as $item) {
            if (in_array($item, $actualCatalogList)) {
                continue;
            } else {
                $actualCatalogList[] = $item;
            }
        }

        return $actualCatalogList;
    }

    /**
     * @param $idCatalog
     *
     * @return array
     */
    protected function getActualBrand($idCatalog)
    {
        $em = $this->getEntityManager();

        // получаем список продуктов выбранной категории
        $query = $em->createQuery('SELECT p FROM Product\Entity\Product p WHERE p.idCatalog = :idCatalog')
            ->setParameter('idCatalog', $idCatalog);

        $listProduct = $query->getResult();

        $actualBrandList = array();

        // получаем сущность бренда из товара,
        // преобразуем из объекта в массив
        // и оставляем уникальные значения
        // array_unique НЕ РАБОТАЕТ
        foreach ($listProduct as $item) {
            $brand = $item->getIdBrand()->getArrayCopy();
            if (in_array($brand, $actualBrandList)) {
                continue;
            } else {
                $actualBrandList[] = $brand;
            }
        }

        return $actualBrandList;
    }


    /**
     * @param $category
     */
    protected function getParentCategory($category)
    {
        // $category может быть как объектом, так и массивом
        // соответственно в $idParent записываем либо свойство, либо ключ
        if (is_array($category)) {
            $idParent = $category['idParent'];
        } else {
            $idParent = $category->getIdParent();
        }

        // рекурсивно проходим по дереву категорий до самого верхнего родителя
        if ($idParent == null) {
            $this->actualCatalogList[] = $category->getArrayCopy();
            return;
        } else {
            if (is_object($category)) {
                $this->actualCatalogList[] = $category->getArrayCopy();
            } else {
                $this->actualCatalogList[] = $category;
            }

            $this->getParentCategory($idParent);
        }
    }

    /**
     * @param $repository
     *
     * @return array
     */
    protected function getLoop($repository)
    {
        $items = $this->getEntityManager()->getRepository($repository)->findAll();

        $tmpArray = array();

        foreach ($items as $item) {
            // Если статус NULL или 3 - показать
            if (is_null($item->getIdStatus()) || ($item->getIdStatus()->getId() == 3)) {
                $tmpArray[] = $item->getArrayCopy();
            }
        }

        return $tmpArray;
    }

    /**
     * @return object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }
}