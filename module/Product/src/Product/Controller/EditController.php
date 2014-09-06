<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Authentication\AuthenticationService;

use Product\Entity\Product as ProductEntity;
use Product\Model\Product;

use Product\Form;

use GoSession;

class EditController extends AbstractActionController
{
    const PRODUCT_ENTITY  = 'Product\Entity\Product';
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';
    const STATUS_ENTITY   = 'Data\Entity\Status';
    const STORE_ENTITY    = 'Data\Entity\Store';

    /**
     * @var
     */
    protected $em;
    protected $fullName;
    private $cache;
    private $currentSession;
    private $currentUser;
    private $translitService;
    private $fullNameService;

    public function __construct()
    {
        $this->currentSession = new Container();

        $auth = new AuthenticationService();
        $this->currentUser = $auth->getIdentity();
    }

    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        // Очистить предыдущие данные кеша
        $this->cache->removeItem('seoUrlParams_'  . $this->currentUser);

        // Получение queryString параметров (array)
        $routeParam = $this->params()->fromRoute();

        //
        $param = $this->getFilterFromRouteParam(array(
                $routeParam['param1'],
                $routeParam['param2']
        ));

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

        $qs = null;

        if (!empty($param)) {
            // Формирование запроса, в зависимости от к-ва параметров
            $qb = $this->getEntityManager()->createQueryBuilder();

            $qs = $qb->select('p')
                ->from(self::PRODUCT_ENTITY, 'p');

            $count = 1;
            foreach ($param as $key => $value) {
                $qb->andWhere('p.id' . ucfirst($key) . ' = ?' . $count)
                    ->setParameter($count, $value);

                $count++;
            }
        }

        // $qs поумолчанию null
        $result = !is_null($qs) ? $qs->getQuery()->getResult() : $qs;

        $res = new ViewModel(array(
            'breadcrumbs' => $breadcrumbs ?: null,
            'result'      => $result
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index',
            array('action' => 'index', 'route' => 'editproduct/seoUrl'));
        $res->addChild($catalog, 'catalog');

        // Записать новые данные сессии в кеш
        $this->cache->addItem('seoUrlParams_' . $this->currentUser, serialize($this->currentSession->seoUrlParams));


        return $res;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
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
                $postData['idSupplier'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $postData['idSupplier']);
                $postData['idCatalog'] = $this->getEntityManager()->
                    find(self::CATEGORY_ENTITY, $postData['idCatalog']);
                $postData['idBrand'] = $this->getEntityManager()->
                    find(self::BRAND_ENTITY, $postData['idBrand']);
                // Вызов сервиса транслитерации
                $postData['translit'] = $this->translitService->getTranslit($postData['name']);

                // empty description fix
                if ($postData['description'] === 'empty') {
                    $postData['description'] = null;
                }

                $product->populate($postData);

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('editproduct');
            }
        }

        $catalog = $this->modifyCatalogOptions();

        return new ViewModel(array(
            'form'     => $form,
            'catalog'  => $catalog,
            'supplier' => $this->setOptionItems('supplier'),
            'brand'    => $this->setOptionItems('brand')
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        // Получить данные из кеша, с учетом идентификатора пользователя и использовать их как параметры redirect
        $seoUrlParams = unserialize($this->cache->getItem('seoUrlParams_' . $this->currentUser));

        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('editproduct');
        }

        $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $id);

        $brandState    = $product->getIdBrand()->getId();
        $catalogState  = $product->getIdCatalog()->getId();
        $supplierState = $product->getIdSupplier()->getId();

        // Перевод в грн.
        $product->setPrice($product->getPrice() / 100);

        $form = $this->getForm();
        $form->setData($product->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $postData = $form->getData();

                $postData['idSupplier'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $postData['idSupplier']);
                $postData['idBrand'] = $this->getEntityManager()->
                    find(self::BRAND_ENTITY, $postData['idBrand']);
                $postData['idCatalog'] = $this->getEntityManager()->
                    find(self::CATEGORY_ENTITY, $postData['idCatalog']);
                // Вызов сервиса транслитерации
                $postData['translit'] = $this->translitService->getTranslit($postData['name']);

                // empty description fix
                if ($postData['description'] === 'empty') {
                    $postData['description'] = null;
                }

                // Обратно в коп.
                $postData['price'] = (int)($postData['price'] * 100);

                $postData['idStatus'] = $product->getIdStatus();
                $postData['indexed']  = $product->getIndexed();
                $postData['img']      = $product->getImg();
                $postData['qty']      = $product->getQty();

                $product->populate($postData);

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('editproduct/seoUrl', $seoUrlParams);
            }
        }

        $catalog = $this->modifyCatalogOptions();

        return new ViewModel(array(
            'form'          => $form,
            'catalog'       => $catalog,
            'supplier'      => $this->setOptionItems('supplier'),
            'brand'         => $this->setOptionItems('brand'),
            'supplierState' => $supplierState,
            'brandState'    => $brandState,
            'catalogState'  => $catalogState,
            'id'            => $id
        ));
    }

    /**
     * @return ViewModel
     */
    public function hideAction()
    {
        // Получить данные из кеша, с учетом идентификатора пользователя и использовать их как параметры redirect
        $seoUrlParams = unserialize($this->cache->getItem('seoUrlParams_' . $this->currentUser));

        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('editproduct');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $hide = $request->getPost('hide', 'Нет');

            if ($hide == 'Да') {
                $id = (int)$request->getPost('id');
                $product = $this->getEntityManager()->find(self::PRODUCT_ENTITY, $id);

                if (is_null($product->getIdStatus()) || ($product->getIdStatus()->getId() === 3)) {
                    $product->setIdStatus($this->getEntityManager()->
                            find(self::STATUS_ENTITY, $id = 4));
                } else {
                    $product->setIdStatus($this->getEntityManager()->
                            find(self::STATUS_ENTITY, $id = 3));
                }

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();
            }

            return $this->redirect()->toRoute('editproduct/seoUrl', $seoUrlParams);
        }

        return new ViewModel(array(
            'id'       => $id,
            'product'  => $this->getEntityManager()->find(self::PRODUCT_ENTITY, $id)
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function imgAction()
    {
        // Получить данные из кеша, с учетом идентификатора пользователя и использовать их как параметры redirect
        $seoUrlParams = unserialize($this->cache->getItem('seoUrlParams_' . $this->currentUser));

        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('editproduct');
        }

        $form = new Form\ImgUploadForm('img-file-form');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);

            if ($form->isValid()) {
                $formData = $form->getData();

                $explode  = explode(DIRECTORY_SEPARATOR, $formData['file']['tmp_name']);
                $fileName = $explode[count($explode) - 1];

                $qb = $this->getEntityManager()->createQueryBuilder();

                // Название изображения | null
                $qs = $qb->select('p.img')
                    ->from(self::PRODUCT_ENTITY, 'p')
                    ->where('p.id = ?1')
                    ->setParameter(1, $id)
                    ->getQuery();
                $qs->execute();

                $qr = $qs->getResult();

                // Удалить старое изображение, если есть
                if (!is_null($qr[0]['img'])) {
                    $this->removeOldImg($qr[0]['img']);
                }

                $qu = $qb->update(self::PRODUCT_ENTITY, 'p')
                    ->set('p.img', '?1')
                    ->where('p.id = ?2')
                    ->setParameter(1, $fileName)
                    ->setParameter(2, $id)
                    ->getQuery();
                $qu->execute();

                return $this->redirect()->toRoute('editproduct/seoUrl', $seoUrlParams);
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id'   => $id
        ));
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
     * Set translit service
     *
     * @param $tanslit
     */
    public function setTranslit($tanslit)
    {
        $this->translitService = $tanslit;
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
    private function getAttributesParams()
    {
        $result = array();

        if (!$this->cache->hasItem('params')) {
            $brand    = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)->findAll();
            $category = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)->findAll();

            foreach ($brand as $brandItem) {
                $result['brand'][$brandItem->getTranslit()] = true;
            }

            foreach ($category as $categoryItem) {
                $result['catalog'][$categoryItem->getTranslit()] = true;
            }

            $this->cache->setItem('params', serialize($result));
        } else {
            $result = unserialize($this->cache->getItem('params'));
        }

        return $result;
    }

    /**
     * Removing old image
     *
     * @param $imageName
     *
     * @return bool
     */
    protected function removeOldImg($imageName)
    {
        $result = unlink('./public/img/product/' . $imageName);

        return $result;
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
            // Пропустить значение элемента-роута поумолчанию (пустая строка)
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
     * Add full name
     *
     * @require @function getFullNameCategory
     * @return array
     */
    protected function modifyCatalogOptions()
    {
        $catalog = $this->setOptionItems('catalog');

        for ($i = 0, $count = count($catalog); $i < $count; $i++) {
            // Сервис - получить полное имя категории
            $catalog[$i]['name'] = $this->fullNameService->getFullNameCategory($catalog[$i]['id']);
            $this->fullNameService->setFullNameToNull();
        }

        return $catalog;
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

            // Убрать родительские категории
            $option_arr = $this->filterCategory($categories);
        } elseif ($type == 'supplier') {
            $suppliers = $this->getEntityManager()
                ->getRepository(self::STORE_ENTITY)->findBy(array('idAttrib' => 3));

            for ($i = 0, $supplier = count($suppliers); $i < $supplier; $i++) {
                $option_arr[$i]['id']   = $suppliers[$i]->getId();
                $option_arr[$i]['name'] = $suppliers[$i]->getName();
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
     * @param $categories
     *
     * @return array
     */
    protected function filterCategory($categories)
    {
        $result        = array();
        $mainCategory  = array();

        foreach ($categories as $categoryItem) {
            if (!is_null($categoryItem->getIdParent())) {
                $mainCategory[] = $categoryItem->getIdParent()->getId();
            }
        }

        $mainCategory = array_unique($mainCategory);

        foreach ($categories as $categoryItem) {
            if (!in_array($categoryItem->getId(), $mainCategory)) {
                // Для того чтобы выставить ключи массива по-порядку
                // потому как array_unique сохраняет значение ключей
                // используем array_push
                array_push(
                    $result,
                    array(
                        'id'   => $categoryItem->getId(),
                        'name' => $categoryItem->getName()
                    )
                );
            }
        }

        return $result;
    }

    /**
     * @param $urlParam
     *
     * @return array
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
            // Сервис - получить полное имя категории
            $breadcrumbs['catalog']['name'] = $this->fullNameService->getFullNameCategory($urlParam['catalog']);
            $this->currentSession->flag['catalog'] = true;
            ++$countParam;
        }

        return $breadcrumbs;
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