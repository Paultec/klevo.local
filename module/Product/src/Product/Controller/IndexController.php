<?php

namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Cart;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY = 'Product\Entity\Product';

    /**
     * @var null|object
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $productList = $this->getEntityManager()
            ->getRepository(self::PRODUCT_ENTITY)
            ->findAll();

        return new ViewModel(array(
            'productList' => $productList,
        ));
    }

    /**
     * @return object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }

}

