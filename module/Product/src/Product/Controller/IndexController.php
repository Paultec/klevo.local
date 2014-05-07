<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY  = 'Product\Entity\Product';

    /**
     * @var
     */
    protected $em;

    public function indexAction()
    {
        $result = $this->getEntityManager()
            ->getRepository(self::PRODUCT_ENTITY)->findBy(array('idCatalog' => 45));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $collection = new ArrayCollection($result);
        $paginator  = new Paginator(new Adapter($collection));
        $paginator
            ->setCurrentPageNumber($page)
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