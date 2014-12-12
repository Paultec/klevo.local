<?php

namespace Article\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

class IndexController extends AbstractActionController
{
    const ARTICLE_ENTITY  = 'Article\Entity\Article';

    protected $em;

    public function indexAction()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb
            ->select(array('a'))
            ->from(self::ARTICLE_ENTITY, 'a')
            ->where(
                $qb->expr()->orX('a.idStatus != 4', 'a.idStatus IS NULL')
            )
            ->orderBy('a.id', 'DESC');

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qs));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(10);

        return new ViewModel(array(
                'paginator' => $paginator
            ));
    }

    public function viewAction()
    {
        $name = (string)$this->params()->fromRoute('name', null);

        if (!$name) {
            return $this->redirect()->toRoute('article');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qs =  $qb
            ->select(array('a'))
            ->from(self::ARTICLE_ENTITY, 'a')
            ->where('a.translit = ?1')
            ->setParameter(1, $name)
            ->getQuery();
        $qs->execute();

        try {
            $qr = $qs->getSingleResult();
        } catch (\Exception $e) {
            $view = new ViewModel();
            $view->setTemplate('error/404');

            return $view;
        }

        return new ViewModel(array(
                'article' => $qr
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