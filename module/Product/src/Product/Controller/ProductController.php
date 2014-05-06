<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProductController extends AbstractActionController
{
    const CATALOG_ENTITY  = 'Catalog\Entity\Catalog';
    const PRODUCT_ENTITY  = 'Product\Entity\Product';

    /**
     * @var
     */
    protected $em;

    public function indexAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $product = $this->getEntityManager()
            ->getRepository(self::PRODUCT_ENTITY)->findBy(array('idCatalog' => $id));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');

        $res = new ViewModel(array(
            'productList' => $product,
            'routeId'     => $id
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