<?php

namespace Data\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;

use Data\Entity\Store as StoreEntity;
use Data\Model\Store;

class StoreController extends AbstractActionController
{
    const STORE_ENTITY     = 'Data\Entity\Store';
    const ATTRIBUTE_ENTITY = 'Data\Entity\Attribute';

    const SUPPLIER_STORE_ATTRIBUTE = 3;

    /**
     * @var
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $re = $this->getEntityManager()->getRepository(self::STORE_ENTITY);
        $suppliers = $re->findBy(array('idAttrib' => self::SUPPLIER_STORE_ATTRIBUTE));

        return new ViewModel(array(
            'suppliers' => $suppliers
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
            $supplier = new StoreEntity();

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $supplier->populate($form->getData());
                $re = $this->getEntityManager()->getRepository(self::ATTRIBUTE_ENTITY);
                $supplierAttribute = $re->find(self::SUPPLIER_STORE_ATTRIBUTE);
                $supplier->setIdAttrib($supplierAttribute);

                $this->getEntityManager()->persist($supplier);
                $this->getEntityManager()->flush();

                // Redirect to list of suppliers
                return $this->redirect()->toRoute('data/store');
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
            return $this->redirect()->toRoute('data/store');
        }

        $supplier = $this->getEntityManager()->find(self::STORE_ENTITY, $id);

        $form = $this->getForm();
        $form->setData($supplier->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $supplier->populate($form->getData());

                $this->getEntityManager()->persist($supplier);
                $this->getEntityManager()->flush();

                // Redirect to list of suppliers
                return $this->redirect()->toRoute('data/store');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id'   => $id
        ));
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Store();
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

