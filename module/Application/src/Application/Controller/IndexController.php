<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY = 'Product\Entity\Product';

    protected $em;
    protected $cache;
    protected $articleLimit = 5;
    protected $topStatus    = 5;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $topProductsArray = array();

        // articles
        if (!$this->cache->hasItem('articles')) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qs =  $qb
                ->select(array('a'))
                ->from('Article\Entity\Article', 'a')
                ->where(
                    $qb->expr()->orX('a.idStatus != 4', 'a.idStatus IS NULL')
                )
                ->orderBy('a.id', 'DESC')
                ->setMaxResults($this->articleLimit)
                ->getQuery();
            $qs->execute();

            $qr = $qs->getArrayResult();

            $this->cache->setItem('articles', serialize($qr));
        } else {
            $qr = unserialize($this->cache->getItem('articles'));
        }

        // top products
        if (!$this->cache->hasItem('topProduct')) {
            $topProducts = $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY)
                ->findBy(array('idStatus' => $this->topStatus));

            $count = 0;
            foreach ($topProducts as $productItem) {
                $topProductsArray[$count]['href']   = $productItem->getTranslit();
                $topProductsArray[$count]['img']    = $productItem->getImg();
                $topProductsArray[$count]['title']  = $productItem->getName();

                $count++;
            }

            $this->cache->setItem('topProduct', serialize($topProductsArray));
        } else {
            $topProductsArray = unserialize($this->cache->getItem('topProduct'));
        }

        $index = new ViewModel(array(
            'articles' => $qr,
            'top'      => array_chunk($topProductsArray, 4)
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index');
        $index->addChild($catalog, 'catalog');

        return $index;
    }

    /**
     * @return ViewModel
     */
    public function paymentDeliveryAction()
    {
        return new ViewModel();
    }

    /**
     * @return ViewModel
     */
    public function contactsAction()
    {
        return new ViewModel();
    }

    /**
     * Set cache from factory
     *
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
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