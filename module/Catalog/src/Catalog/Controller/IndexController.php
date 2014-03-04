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
            ->getRepository('\Catalog\Entity\Item')
            ->findBy(array());

        $items_arr = array();

        foreach ($items as $item) {
            $items_arr[] = $item->getArrayCopy();
        }

        $categoryList = array();

        foreach ($items_arr as $item) {
            $categoryList[] = $item;
        }

        return new ViewModel(array(
            //'items' => $items_arr,
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