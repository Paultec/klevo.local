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
        $brandList    = $this->getLoop('Catalog\Entity\Brand');
        $categoryList = $this->getLoop('Catalog\Entity\Category');

        return new ViewModel(array(
            'brandList'    => $brandList,
            'categoryList' => $categoryList,
        ));
    }

    /**
     * @param $repository
     * @return array
     */
    protected function getLoop($repository)
    {
        $items = $this->getEntityManager()
            ->getRepository($repository)
            ->findBy(array());

        $tmpArray = array();

        foreach ($items as $item) {
            $tmpArray[] = $item->getArrayCopy();
        }

        return $tmpArray;
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