<?php
namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var null|object
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $items = $this->getEntityManager()
            ->getRepository('\Catalog\Entity\Category')
            ->findBy(array());

        $categoryList = array();

        foreach ($items as $item) {
            $categoryList[] = $item->getArrayCopy();
        }

        $brands = $this->getEntityManager()
            ->getRepository('\Catalog\Entity\Brand')
            ->findBy(array());

        $brandList = array();

        foreach ($brands as $brand) {
            $brandList[] = $brand->getArrayCopy();
        }

        return new ViewModel(array(
            'brandList'    => $brandList,
            'categoryList' => $categoryList,
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