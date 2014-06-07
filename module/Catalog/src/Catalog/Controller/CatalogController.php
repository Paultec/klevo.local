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
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';
    const STATUS_ENTITY   = 'Data\Entity\Status';

    /**
     * @var
     */
    protected $em;
    protected $fullName;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        // С категорией "Главная" (param::index)
        return new ViewModel(array(
            'categories' => $this->modifyCatalogOptions('index')
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
                    find(self::CATEGORY_ENTITY, $postData['idParent']);

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('category');
            }
        }

        return new ViewModel(array(
            'form'       => $form,
            'categories' => $this->modifyCatalogOptions()
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

        $catalog = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $id);

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
                    find(self::CATEGORY_ENTITY, $postData['idParent']);

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('category');
            }
        }

        return new ViewModel(array(
            'form'       => $form,
            'id'         => $id,
            'parentState'=> $parentState,
            'categories' => $this->modifyCatalogOptions()
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function hideAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('category');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $hide = $request->getPost('hide', 'Нет');

            if ($hide == 'Да') {
                $id = (int)$request->getPost('id');
                $category = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $id);

                if (is_null($category->getIdStatus()) || ($category->getIdStatus()->getId() === 3)) {
                    $category->setIdStatus($this->getEntityManager()->
                        find(self::STATUS_ENTITY, $id = 4));
                } else {
                    $category->setIdStatus($this->getEntityManager()->
                        find(self::STATUS_ENTITY, $id = 3));
                }

                if ($category) {
                    $this->getEntityManager()->persist($category);
                    $this->getEntityManager()->flush();

                    // скрыть подкатегории
                    $this->childHide($category);
                }
            }

            // Redirect to list of category
            return $this->redirect()->toRoute('category');
        }

        return new ViewModel(array(
            'id'       => $id,
            'category' => $this->getEntityManager()->find(self::CATEGORY_ENTITY, $id)
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
//    public function deleteAction()
//    {
//        $id = (int) $this->params()->fromRoute('id', 0);
//
//        if (!$id) {
//            return $this->redirect()->toRoute('category');
//        }
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//
//            $del = $request->getPost('del', 'Нет');
//
//            if ($del == 'Да') {
//                $id = (int)$request->getPost('id');
//                $category = $this->getEntityManager()
//                    ->find(self::CATEGORY_ENTITY, $id);
//                if ($category) {
//                    $this->getEntityManager()->remove($category);
//                    $this->getEntityManager()->flush();
//                }
//            }
//
//            // Redirect to list of category
//            return $this->redirect()->toRoute('category');
//        }
//
//        return new ViewModel(array(
//            'id'       => $id,
//            'category' => $this->getEntityManager()
//                    ->find(self::CATEGORY_ENTITY, $id)
//        ));
//    }


    /**
     * Add full name
     *
     * @require @function getFullNameCategory
     *
     * @param null $param
     *
     * @return \Zend\Http\Response|ViewModel
     */
    protected function modifyCatalogOptions($param = null)
    {
        $catalog = $this->setOptionItems();

        for ($i = 0, $count = count($catalog); $i < $count; $i++) {
            $catalog[$i]['name'] = $this->getFullNameCategory($catalog[$i]['id']);

            $this->fullName = null;
        }

        if (is_null($param)) {
            array_unshift($catalog, array(
                'id'   => null,
                'name' => 'Главная'
            ));
        }

        return $catalog;
    }

    /**
     * @return array
     */
    protected function setOptionItems()
    {
        $categories = $this->getEntityManager()
            ->getRepository(self::CATEGORY_ENTITY)->findAll();

        $option_arr = array();

        for ($i = 0, $category = count($categories); $i < $category; $i++) {
            $option_arr[$i]['id']     = $categories[$i]->getId();
            $option_arr[$i]['name']   = $categories[$i]->getName();

            if (!is_null($categories[$i]->getIdStatus())) {
                $option_arr[$i]['status'] = $categories[$i]->getIdStatus()->getId();
            } else {
                $option_arr[$i]['status'] = null;
            }
        }

        return $option_arr;
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
     * @param $category
     */
    protected function childHide($category)
    {
        $childCategory = $this->getEntityManager()->getRepository(self::CATEGORY_ENTITY)->
            findBy(array('idParent' => $category->getId()));

        if (!empty($childCategory)) {
            foreach ($childCategory as $child) {
                if ($category->getIdStatus()->getId() === 3) {
                    $child->setIdStatus($this->getEntityManager()->
                            find(self::STATUS_ENTITY, $id = 3));
                } else {
                    $child->setIdStatus($this->getEntityManager()->
                            find(self::STATUS_ENTITY, $id = 4));
                }

                $this->getEntityManager()->persist($child);
            }

            $this->getEntityManager()->flush();
        }
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