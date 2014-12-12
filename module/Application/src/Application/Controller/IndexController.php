<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    protected $articleLimit = 5;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $cache = $this->getServiceLocator()->get('filesystem');

        //articles
        if (!$cache->hasItem('articles')) {
            $qb = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->createQueryBuilder();
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

            $cache->setItem('articles', serialize($qr));
        } else {
            $qr = unserialize($cache->getItem('articles'));
        }

        $index = new ViewModel(array(
                'articles' => $qr
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
}