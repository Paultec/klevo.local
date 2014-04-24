<?php
namespace Catalog\Controller;

use ArrayObject;

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
                $catalog->populate($form->getData());

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

//            $option_arr[0] = 'Главная';
//            $option_arr[$categories[$i]->getId()] = $categories[$i]->getName();
        }

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