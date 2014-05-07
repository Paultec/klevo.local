<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;

class ProducerController extends AbstractActionController
{
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const PRODUCT_ENTITY  = 'Product\Entity\Product';

    /**
     * @var
     */
    protected $em;

    public function indexAction()
    {
        $id = (int)$this->params()->fromRoute('id');

        $producer = $this->getEntityManager()
            ->getRepository(self::PRODUCT_ENTITY)->findBy(array('idBrand' => $id));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');

        // Pagination
        $collection = new ArrayCollection($producer);
        $paginator  = new Paginator(new Adapter($collection));
        $paginator
            ->setCurrentPageNumber(1)
            ->setItemCountPerPage(12);

        $res = new ViewModel(array(
            'paginator' => $paginator
        ));

        $res->addChild($catalog, 'catalog');

        return $res;
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