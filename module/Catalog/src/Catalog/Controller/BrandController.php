<?php

namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Catalog\Entity\Brand as BrandEntity;
use Catalog\Model\Brand;

class BrandController extends AbstractActionController
{
    const BRAND_ENTITY   = 'Catalog\Entity\Brand';
    const PRODUCT_ENTITY = 'Product\Entity\Product';
    const STATUS_ENTITY  = 'Data\Entity\Status';

    /**
     * @var
     */
    protected $em;
    private $cache;
    private $translitService;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $re = $this->getEntityManager()->getRepository(self::BRAND_ENTITY);
        $brands = $re->findAll();

        return new ViewModel(array(
            'brands' => $brands
        ));
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $form = $this->getForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $brand = new BrandEntity();

            $form->setData($request->getPost());
            if ($form->isValid()) {
                // Вызов сервиса транслитерации
                $postData = $form->getData();

                // проверить, нет ли уже такого производителя
                $currentBrands      = $this->getEntityManager()->getRepository(self::BRAND_ENTITY)->findAll();
                $currentBrandsArray = $this->getCurrentBrandsName($currentBrands);

                if (!isset($currentBrandsArray[strtolower($postData['name'])])) {
                    $postData['translit'] = $this->translitService->getTranslit($postData['name']);

                    $brand->populate($postData);

                    $this->getEntityManager()->persist($brand);
                    $this->getEntityManager()->flush();

                    // Очистить кэш с параметрами производителей
                    $this->cache->removeItem('params');
                }

                // Redirect to list of brands
                return $this->redirect()->toRoute('brand');
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $id);

        $form = $this->getForm();
        $form->setData($brand->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                // Вызов сервиса транслитерации
                $postData = $form->getData();
                $postData['translit'] = $this->translitService->getTranslit($postData['name']);

                $brand->populate($postData);

                $this->getEntityManager()->persist($brand);
                $this->getEntityManager()->flush();

                // Очистить кэш с параметрами производителей
                $this->cache->removeItem('params');

                // Redirect to list of brands
                return $this->redirect()->toRoute('brand');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id'   => $id
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function hideAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $hide = $request->getPost('hide', 'Нет');

            if ($hide == 'Да') {
                $id = (int)$request->getPost('id');

                $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $id);

                // set status (null or 3 for show, 4 for hidden)
                if (!is_null($brand->getIdStatus())) {
                    $idStatus = $brand->getIdStatus()->getId();

                    if (is_null($idStatus) || $idStatus == 3) {
                        $idStatus = 4;
                    } else {
                        $idStatus = 3;
                    }
                } else {
                    $idStatus = 4;
                }

                 //UPDATE Brand
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::BRAND_ENTITY, 'b')
                    ->set('b.idStatus', '?1')
                    ->where('b.id = ?2')
                    ->setParameter(1, $idStatus)
                    ->setParameter(2, $id)
                    ->getQuery();
                $qu->execute();

                // UPDATE Product
                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::PRODUCT_ENTITY, 'p')
                    ->set('p.idStatus', '?1')
                    ->where('p.idBrand = ?2')
                    ->setParameter(1, $idStatus)
                    ->setParameter(2, $id)
                    ->getQuery();
                $qu->execute();
            }

            // Redirect to list of brand
            return $this->redirect()->toRoute('brand');
        }

        return new ViewModel(array(
            'id'    => $id,
            'brand' => $this->getEntityManager()->find(self::BRAND_ENTITY, $id)
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
     * @param array $brands
     *
     * @return array
     */
    protected function getCurrentBrandsName(array $brands)
    {
        $resultArr = array();

        foreach ($brands as $brand) {
            $resultArr[strtolower($brand->getName())] = true;
        }

        return $resultArr;
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Brand();
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