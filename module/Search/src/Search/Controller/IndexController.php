<?php

namespace Search\Controller;

ini_set('max_execution_time', 7200);

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;
use Zend\Session\Container;

use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene\Search\QueryParser;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;

use GoSession;

class IndexController extends AbstractActionController
{
    const PRODUCT_ENTITY        = 'Product\Entity\Product';
    const PRODUCT_ENTITY_QTY    = 'Product\Entity\ProductCurrentQty';

    protected $em;
    protected $idIndexed = array();
    private $currentSession;

    public function __construct()
    {
        $this->currentSession = new Container();
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        // если нет строки запроса, редирект на главную
        $param = $this->params()->fromQuery();

        if (is_null($param['q'])) {
            return $this->redirect()->toRoute('home');
        }

        // Выбираем текст из строки запроса
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

        $field = array();

        foreach ($hits as $result) {
            // выставляем минимальный вес релевантности
            if ($result->score > 0.7) {
                $field[] = $result->idProduct;
            }
        }

        // add field function in Doctrine
        $doctrineConfig = $this->getEntityManager()->getConfiguration();
        $doctrineConfig->addCustomStringFunction('FIELD', 'DoctrineExtensions\Query\Mysql\Field');

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb
            ->select('p, field(p.id, ?1) as HIDDEN field', 'q.qty as quantity', 'q.virtualQty')
            ->from(self::PRODUCT_ENTITY, 'p')
            ->join(
                self::PRODUCT_ENTITY_QTY, 'q',
                'WITH', 'p.id = q.idProduct'
            )
            ->where($qb->expr()->in('p.id', '?1'))
            ->andWhere('p.price != 0')
            ->andWhere(
                $qb->expr()->orX('p.idStatus != 4', 'p.idStatus IS NULL')
            )
            ->setParameter(1, $field)
            ->orderBy('field');

        // Pagination
        $matches = $this->getEvent()->getRouteMatch();
        $page    = $matches->getParam('page', 1);

        $adapter   = new DoctrineAdapter(new ORMPaginator($qs, false));
        $paginator = new Paginator($adapter);

        $paginator
            ->setCurrentPageNumber($page)
            ->setItemCountPerPage(24);

        // id товаров, которые уже в корзине
        $inCart = isset($this->currentSession->cart) ? $this->currentSession->cart : array();

        // данные пользователя
        $userInfo = $this->forward()->dispatch('Data/Controller/CartUserHelp',
            array('action' => 'user'))->getVariables();

        $res = new ViewModel(array(
            'paginator' => $paginator ?: null,
            'userInfo'  => $userInfo,
            'inCart'    => $inCart
        ));

        $catalog = $this->forward()->dispatch('Catalog\Controller\Index', array('action' => 'index'));

        $res->addChild($catalog, 'catalog');

        return $res;
    }

    /**
     * @return ViewModel
     */
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

    /**
     * @return ViewModel
     */
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

    public function preSearchAction()
    {
        $limit = 10;

        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('home');
        }

        $postData = $request->getPost();
        $str = $postData['str'];

        // установка необходимой кодировки для поиска
        \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
            new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8\CaseInsensitive()
        );

        \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');

        // Поиск запроса в индексе
        $searchIndexLocation = $this->getIndexLocation();
        $index = Lucene::open($searchIndexLocation);
        $query = QueryParser::parse($str, 'UTF-8');

        $hits = $index->find($query);

        $field = array();

        foreach ($hits as $result) {
            // выставляем минимальный вес релевантности
            if ($result->score > 0.7) {
                $field[] = $result->idProduct;
            }
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qs = $qb->select('p')
            ->from(self::PRODUCT_ENTITY, 'p')
            ->where($qb->expr()->in('p.id', '?1'))
            ->andWhere('p.price != 0')
            ->andWhere(
                $qb->expr()->orX('p.idStatus != 4', 'p.idStatus IS NULL')
            )
            ->setMaxResults($limit)
            ->setParameter(1, $field)
            ->getQuery();

        $qr = $qs->getArrayResult();

        return new JsonModel(array(
            'result' => json_encode($qr)
        ));
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
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

