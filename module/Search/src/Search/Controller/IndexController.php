<?php

namespace Search\Controller;

ini_set('max_execution_time', 7200);

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\QueryParser;

use Doctrine\Common\Collections\ArrayCollection;
use DoctrineModule\Paginator\Adapter\Collection as Adapter;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY = 'Product\Entity\Product';

    protected $em;
    protected $idIndexed = array();

    public function indexAction()
    {
        // если нет строки запроса, редирект на главную
        $param = $this->params()->fromQuery();

        if (is_null($param['q'])) {
            return $this->redirect()->toRoute('home');
        }

        // выбираем текст из строки запроса
        $request  = $this->getRequest();
        $queryStr = $request->getQuery()->get('q');

        // установка необходимой кодировки для поиска
        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive()
        );

        \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');

        // Поиск запроса в индексе
        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene::open($searchIndexLocation);
        $query = QueryParser::parse($queryStr, 'UTF-8');

        $hits = $index->find($query);

        $tmpArr = array();

        foreach ($hits as $result) {
            // выставляем минимальный вес релевантности
            if ($result->score > 0.7) {
                $tmpArr[] = $result->idProduct;
            }
        }

        if (!empty($tmpArr)) {
            $temp   = array();
            $result = array();

            $qb = $this->getEntityManager()->createQueryBuilder();

            $qs = $qb->select('p')
                ->from(self::PRODUCT_ENTITY, 'p')
                ->where($qb->expr()->in('p.id', $tmpArr))
                ->andWhere('p.price > ?1')
                ->setParameter(1, 0)
                ->getQuery();
            $qs->execute();

            $qr = $qs->getResult();

            if (!empty($qr)) {
                foreach ($qr as $item) {
                    $temp[] = $item->getId();
                }

//                var_dump($tmpArr);

                // вывод в порядке весов релевантности
                foreach ($tmpArr as $value) {
                    $result[] = $qr[array_search($value, $temp)];
                }

//                exit;

                // Pagination
                $matches = $this->getEvent()->getRouteMatch();
                $page    = $matches->getParam('page', 1);

                $adapter   = new ArrayCollection($result);
                $paginator = new Paginator(new Adapter($adapter));

                $paginator
                    ->setCurrentPageNumber($page)
                    ->setItemCountPerPage(24);
            } else {
                $paginator = null;
            }
        } else {
            $paginator = null;
        }

        $res = new ViewModel(array(
            'paginator' => $paginator,
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    public function createAction()
    {
        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive()
        );

        \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');

        $searchIndexLocation = $this->getIndexLocation();

        try {
            $index = Lucene::open($searchIndexLocation);
        } catch(\Exception $e) {
            $index = Lucene::create($searchIndexLocation);
        }

        $qr = $this->getResultSet();

        if ($qr) {
            foreach ($qr as $row) {
                // создание полей lucene
                $id   = Document\Field::unIndexed('idProduct', $row['id'], 'UTF-8');
                $name = Document\Field::Text('name', $row['name'], 'UTF-8');

                // создание нового документа и добавление всех полей
                $indexDoc = new Document();

                $indexDoc->addField($id);
                $indexDoc->addField($name);

                $index->addDocument($indexDoc);
            }

            $index->commit();
//            unset($index);

            // запись в базу флага о индексации
            $qb = $this->getEntityManager()->createQueryBuilder();

            $qu = $qb->update(self::PRODUCT_ENTITY, 'p')
                ->set('p.indexed', '?1')
                ->where($qb->expr()->in('p.id', $this->idIndexed))
                ->setParameter(1, 1)
                ->getQuery();
            $qu->execute();
        }

        return new ViewModel();
    }

    public function optimizeAction()
    {
        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive()
        );

        \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');

        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene::open($searchIndexLocation);

        $index->optimize();

        return new ViewModel();
    }

    protected function getResultSet()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb->select('p.id, p.name')
            ->from(self::PRODUCT_ENTITY, 'p')
            ->where('p.indexed IS NULL')
            ->getQuery();
        $qs->execute();

        // Результирующий набор для индексирования
        $qr = $qs->getResult();

        if (!empty($qr)) {
            foreach ($qr as $row) {
                $this->idIndexed[] = $row['id'];
            }

            return $qr;
        }

        return false;
    }

    protected function getIndexLocation()
    {
        // выборка конфигурации из конфигурационных данных модуля
        $config = $this->getServiceLocator()->get('config');

        if (!empty($config['module_config']['search_index'])) {
            return $config['module_config']['search_index'];
        } else {
            return false;
        }
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }
}

