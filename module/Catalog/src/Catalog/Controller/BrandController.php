<?php

namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;

use Catalog\Entity\Brand as BrandEntity;
use Catalog\Model\Brand;

class BrandController extends AbstractActionController
{
    const BRAND_ENTITY  = 'Catalog\Entity\Brand';
    const STATUS_ENTITY = 'Data\Entity\Status';

    /**
     * @var
     */
    protected $em;

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
                $brand->populate($form->getData());

                $this->getEntityManager()->persist($brand);
                $this->getEntityManager()->flush();

                $this->clearCache();

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
                $brand->populate($form->getData());

                $this->getEntityManager()->persist($brand);
                $this->getEntityManager()->flush();

                $this->clearCache();

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

                if (is_null($brand->getIdStatus()) || ($brand->getIdStatus()->getId() === 3)) {
                    $brand->setIdStatus($this->getEntityManager()->
                        find(self::STATUS_ENTITY, $id = 4));
                } else {
                    $brand->setIdStatus($this->getEntityManager()->
                        find(self::STATUS_ENTITY, $id = 3));
                }

                if ($brand) {
                    $this->getEntityManager()->persist($brand);
                    $this->getEntityManager()->flush();
                }
            }

            $this->clearCache();

            // Redirect to list of brand
            return $this->redirect()->toRoute('brand');
        }

        return new ViewModel(array(
            'id'    => $id,
            'brand' => $this->getEntityManager()->find(self::BRAND_ENTITY, $id)
        ));
    }

    /**
     * @return ViewModel
     */
//    public function deleteAction()
//    {
//        $id = (int) $this->params()->fromRoute('id', 0);
//
//        if (!$id) {
//            return $this->redirect()->toRoute('brand');
//        }
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//
//            $del = $request->getPost('del', 'Нет');
//
//            if ($del == 'Да') {
//                $id = (int)$request->getPost('id');
//                $brand = $this->getEntityManager()->find(self::BRAND_ENTITY, $id);
//                if ($brand) {
//                    $this->getEntityManager()->remove($brand);
//                    $this->getEntityManager()->flush();
//                }
//            }
//
//             $this->clearCache();
//
//            // Redirect to list of brand
//            return $this->redirect()->toRoute('brand');
//        }
//
//        return new ViewModel(array(
//            'id'    => $id,
//            'brand' => $this->getEntityManager()->find(self::BRAND_ENTITY, $id)
//        ));
//    }

    /**
     * @param string $cacheKey
     * @return ViewModel
     */
    public function clearCache($cacheKey = 'brands')
    {
        if ($this->getServiceLocator()->get('filesystem')->hasItem($cacheKey)) {
            $this->getServiceLocator()->get('filesystem')->removeItem($cacheKey);

            return true;
        }

        return false;
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
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
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