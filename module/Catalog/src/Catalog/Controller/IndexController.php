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
        if (!$this->hasCacheItem('brands')) {
            $this->getLoop(self::BRAND_ENTITY, 'brands');
        }

        if (!$this->hasCacheItem('categories')) {
            $this->getLoop(self::CATEGORY_ENTITY, 'categories');
        }

        return new ViewModel(array(
            'brandList'    => $this->getFromCache('brands'),
            'categoryList' => $this->getFromCache('categories'),
        ));
    }

    /**
     * @param $item
     * @return mixed|string
     */
    protected function getFromCache($item)
    {
        if ($this->hasCacheItem($item)) {
            $result = $this->getServiceLocator()->get('filesystem')->getItem($item);

            return unserialize($result);
        }

        return 'There is no such key!';
    }

    /**
     * @param $item
     * @return bool
     */
    protected function hasCacheItem($item)
    {
        if ($this->getServiceLocator()->get('filesystem')->hasItem($item)) {
            return true;
        }

        return false;
    }

    /**
     * @param $repository
     * @param $cacheKey
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

        $this->getServiceLocator()->get('filesystem')
            ->setItem($cacheKey, serialize($tmpArray));

        return;
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