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
            ->findBy(array('idParent' => null));

        $items_arr = array();

        foreach ($items as $item) {
            $items_arr[] = $item->getArrayCopy();
        }

        return new ViewModel(array(
            'items' => $items_arr,
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