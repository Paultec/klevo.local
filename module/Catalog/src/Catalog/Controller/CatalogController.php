<?php
namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;

use Catalog\Entity\Catalog as CatalogEntity;
use Catalog\Model\Catalog;

class CatalogController extends AbstractActionController
{
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';
    const PRODUCT_ENTITY  = 'Product\Entity\Product';
    const STATUS_ENTITY   = 'Data\Entity\Status';

    /**
     * @var
     */
    protected $em;
    protected $child = array();
    private $cache;
    private $translitService;
    private $fullNameService;

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
                // Вызов сервиса транслитерации
                $postData['translit'] = $this->translitService->getTranslit($this->fullNameService->getFullNameCategory($postData['id']));

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Очистить кэш с параметрами категорий
                $this->cache->removeItem('params');

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
                // Вызов сервиса транслитерации
                $postData['translit'] = $this->translitService->getTranslit($this->fullNameService->getFullNameCategory($postData['id']));

                $catalog->populate($postData);

                $this->getEntityManager()->persist($catalog);
                $this->getEntityManager()->flush();

                // Очистить кэш с параметрами категорий
                $this->cache->removeItem('params');

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

                // set status (null or 3 for show, 4 for hidden)
                if (!is_null($category->getIdStatus())) {
                    $idStatus = $category->getIdStatus()->getId();

                    if (is_null($idStatus) || $idStatus == 3) {
                        $idStatus = 4;
                    } else {
                        $idStatus = 3;
                    }
                } else {
                    $idStatus = 4;
                }

                $catalogChild = $this->getCatalogChild($id);
                array_unshift($catalogChild, $id);

                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::CATEGORY_ENTITY, 'c')
                    ->set('c.idStatus', '?1')
                    ->where($qb->expr()->in('c.id', $catalogChild))
                    ->setParameter(1, $idStatus)
                    ->getQuery();
                $qu->execute();

                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::PRODUCT_ENTITY, 'p')
                    ->set('p.idStatus', '?1')
                    ->where($qb->expr()->in('p.idCatalog', $catalogChild))
                    ->setParameter(1, $idStatus)
                    ->getQuery();
                $qu->execute();
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
     * @param $id
     *
     * @return array
     */
    protected function getCatalogChild($id)
    {
        $tmpArr = array();

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb->select('c.id')
            ->from(self::CATEGORY_ENTITY, 'c')
            ->where($qb->expr()->in('c.idParent', $id))
            ->getQuery();
        $qs->execute();

        $qr = $qs->getResult();

        foreach ($qr as $result) {
            $this->child[] = $result['id'];

            $tmpArr[] = $result['id'];
        }

        if (!empty($qr)) {
            $this->getCatalogChild($tmpArr);
        }

        return $this->child;
    }

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
            $catalog[$i]['name'] = $this->fullNameService->getFullNameCategory($catalog[$i]['id']);

            $this->fullNameService->setFullNameToNull();
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
            $option_arr[$i]['id']   = $categories[$i]->getId();
            $option_arr[$i]['name'] = $categories[$i]->getName();

            if (!is_null($categories[$i]->getIdStatus())) {
                $option_arr[$i]['status'] = $categories[$i]->getIdStatus()->getId();
            } else {
                $option_arr[$i]['status'] = null;
            }
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