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
    /**
     * @var
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $re = $this->getEntityManager()->getRepository('Catalog\Entity\Brand');
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

        $brand = $this->getEntityManager()->find('Catalog\Entity\Brand', $id);

        $form = $this->getForm();
        $form->setData($brand->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $brand->populate($form->getData());

                $this->getEntityManager()->persist($brand);
                $this->getEntityManager()->flush();

                // Redirect to list of brands
                $this->redirect()->toRoute('brand');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id
        ));
    }

    /**
     * @return ViewModel
     */
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $del = $request->getPost('del', 'Нет');

            if ($del == 'Да') {
                $id = (int)$request->getPost('id');
                $brand = $this->getEntityManager()->find('Catalog\Entity\Brand', $id);
                if ($brand) {
                    $this->getEntityManager()->remove($brand);
                    $this->getEntityManager()->flush();
                }
            }

            // Redirect to list of brand
            return $this->redirect()->toRoute('brand');
        }

        return new ViewModel(array(
            'id'    => $id,
            'brand' => $this->getEntityManager()->find('Catalog\Entity\Brand', $id)
        ));
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