<?php
namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Doctrine\ORM\EntityManager;

use Catalog\Entity\Catalog as CatalogEntity;
use Catalog\Model\Catalog;

class CatalogController extends AbstractActionController
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
        $re = $this->getEntityManager()->getRepository('Catalog\Entity\Catalog');
        $categories = $re->findAll();

        return new ViewModel(array(
            'categories' => $categories
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
            $catalog = new CatalogEntity();

            $form->setData($request->getPost());

            if ($form->isValid()) {
                $postData = $form->getData();
                $postData['idParent'] = $this->getEntityManager()->
                    find('Catalog\Entity\Catalog', $postData['idParent']);

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('category');
            }
        }

        return new ViewModel(array(
            'form'       => $form,
            'categories' => $this->setOptionItems()
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('category');
        }

        $catalog = $this->getEntityManager()->find('Catalog\Entity\Catalog', $id);

        $form = $this->getForm();
        $form->setData($catalog->getArrayCopy());

        if (is_null($catalog->getIdParent())) {
            $parentState = $catalog->getIdParent();
        } else {
            $parentState = $catalog->getIdParent()->getId();
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $postData = $form->getData();
                $postData['idParent'] = $this->getEntityManager()->
                    find('Catalog\Entity\Catalog', $postData['idParent']);

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Redirect to list of categories
                $this->redirect()->toRoute('category');
            }
        }

        return new ViewModel(array(
            'form'       => $form,
            'id'         => $id,
            'parentState'=> $parentState,
            'categories' => $this->setOptionItems()
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('category');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $del = $request->getPost('del', 'Нет');

            if ($del == 'Да') {
                $id = (int)$request->getPost('id');
                $category = $this->getEntityManager()->find('Catalog\Entity\Catalog', $id);
                if ($category) {
                    $this->getEntityManager()->remove($category);
                    $this->getEntityManager()->flush();
                }
            }

            // Redirect to list of category
            return $this->redirect()->toRoute('category');
        }

        return new ViewModel(array(
            'id'       => $id,
            'category' => $this->getEntityManager()->find('Catalog\Entity\Catalog', $id)
        ));
    }

    /**
     * @return array
     */
    protected function setOptionItems()
    {
        $categories = $this->getEntityManager()
            ->getRepository('Catalog\Entity\Catalog')->findAll();

        $option_arr = array();

        for ($i = 0, $category = count($categories); $i < $category; $i++) {
            $option_arr[$i]['id']   = $categories[$i]->getId();
            $option_arr[$i]['name'] = $categories[$i]->getName();
        }

        array_unshift($option_arr, array(
            'id'   => null,
            'name' => 'Главная'
        ));

        return $option_arr;
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Catalog();
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