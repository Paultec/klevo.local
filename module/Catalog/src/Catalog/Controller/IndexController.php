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

        return new ViewModel(array(
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