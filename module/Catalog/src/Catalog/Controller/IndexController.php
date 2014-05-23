<?php
namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';

    /**
     * @var null|object
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'brandList'    => $this->getLoop(self::BRAND_ENTITY, 'brands'),
            'categoryList' => $this->getLoop(self::CATEGORY_ENTITY, 'categories'),
        ));
    }

    /**
     * @param $repository
     * @param $cacheKey
     *
     * @return array
     */
    protected function getLoop($repository, $cacheKey)
    {
        $items = $this->getEntityManager()
            ->getRepository($repository)
            ->findAll();

        $tmpArray = array();

        foreach ($items as $item) {
            // Если статус NULL или 3 - показать
            if (is_null($item->getIdStatus()) || ($item->getIdStatus()->getId() == 3)) {
                $tmpArray[] = $item->getArrayCopy();
            }
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