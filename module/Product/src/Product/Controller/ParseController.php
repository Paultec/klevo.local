<?php
namespace Product\Controller;

ini_set('max_execution_time', 7200);

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use Zend\Session\Container;

use Product\Entity\Product;

use GoSession;

use PHPExcel_IOFactory;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Settings;

class ParseController extends AbstractActionController
{
    const PRODUCT_ENTITY     = 'Product\Entity\Product';
    const PRODUCT_ENTITY_QTY = 'Product\Entity\ProductCurrentQty';
    const CATEGORY_ENTITY    = 'Catalog\Entity\Catalog';
    const BRAND_ENTITY       = 'Catalog\Entity\Brand';
    const STORE_ENTITY       = 'Data\Entity\Store';

    /**
     * @var
     */
    protected $em;

    protected $inputFileName;
    protected $inputFileType;

    protected $insertRow    = 0;
    protected $skipRow      = 0;
    protected $errorRow     = 0;
    protected $errorData    = array();
    protected $skipRowData  = array();

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
        $this->getFile();
        $this->fileInfo($this->inputFileName);

        $parseType = $this->currentSession->parseType;

        $update = null;
        if ($parseType === 'insert') {
            // insert
            $this->insertData();
        } else {
            // update
            $update = $this->updateData();
        }

        return new ViewModel(array(
            'update'        => $update,
            'insertRow'     => $this->insertRow,
            'skipRow'       => $this->skipRow,
            'errorRow'      => $this->errorRow,
            'errorData'     => $this->errorData,
            'skipRowData'   => $this->skipRowData
        ));
    }

    /**
     * Insert data
     *
     * Insert products form Excel into DB
     */
    protected function insertData()
    {
        $currentData = $this->getCurrentData();

        $parse = $this->parseExcel();
        array_shift($parse);

        // Идентификаторы
        $categoryIds = $this->getEntityId($entity = self::CATEGORY_ENTITY);
        $brandIds    = $this->getEntityId($entity = self::BRAND_ENTITY);
        $storeIds    = $this->getEntityId($entity = self::STORE_ENTITY);

        $prepareData = array();

        // Вызов сервиса транслитерации
        $translit = $this->getServiceLocator()->get('translitService');

        foreach ($parse as $dataRow) {
            if (!isset($currentData[strtolower(trim($dataRow[0]))])) {
                $product = new Product();

                try {
                    // Выбросить исключение, если не числовой тип
                    if ((gettype($dataRow[2]) !== 'double' || !in_array((int)$dataRow[2], $storeIds)) ||
                        (gettype($dataRow[3]) !== 'double' || !in_array((int)$dataRow[3], $brandIds)) ||
                        (gettype($dataRow[4]) !== 'double' || !in_array((int)$dataRow[4], $categoryIds)) ||
                         gettype($dataRow[5]) !== 'double'
                    ) {
                        throw new \Exception('Invalid data type');
                    }

                    $prepareData['name']        = $dataRow[0];
                    $prepareData['translit']    = $translit->getTranslit($dataRow[0]);
                    $prepareData['description'] = $dataRow[1] ?: null;
                    $prepareData['idSupplier']  = $this->getEntityManager()->find(self::STORE_ENTITY, $dataRow[2]);
                    $prepareData['idBrand']     = $this->getEntityManager()->find(self::BRAND_ENTITY, $dataRow[3]);
                    $prepareData['idCatalog']   = $this->getEntityManager()->find(self::CATEGORY_ENTITY, $dataRow[4]);
                    $prepareData['price']       = (int)($dataRow[5] * 100);

                    $product->populate($prepareData);

                    $this->getEntityManager()->persist($product);
                } catch (\Exception $e) {
                    $this->errorRow++;
                    $this->errorData[] = $dataRow[0];

                    continue;
                }

                $this->insertRow++;
            } else {
                $this->skipRow++;
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Update data
     *
     * Update virtual quantity from supplier price
     *
     * @return bool
     */
    protected function updateData()
    {
        $matchProducts  = array();
        $productIds     = array();

        $currentData = $this->getCurrentData();

        // получить идентификатор поставщика idSupplier
        $fullData   = $this->parseExcel();
        $idSupplier = (int)$fullData[0][1];

        $supplier   = $this->getEntityManager()->getRepository(self::STORE_ENTITY)->findBy(array('id' => $idSupplier));
        $product    = $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY)->findBy(array('idSupplier' => $idSupplier));

        $nullifyId = array();

        foreach ($product as $item) {
            $nullifyId[] = $item->getId();
        }

        if (empty($supplier) || empty($nullifyId)) {
            return false;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qu = $qb->update(self::PRODUCT_ENTITY_QTY, 'q')
            ->set('q.virtualQty', '?1')
            ->where($qb->expr()->in('q.id', $nullifyId))
            ->setParameter(1, 0)
            ->getQuery();
        $qu->execute();

        // Удаляем шапку документа
        $parse = array_slice($fullData, 2);

        foreach ($parse as $dataRow) {
            if (isset($currentData[strtolower(trim($dataRow[0]))])) {
                try {
                    if (gettype($dataRow[0]) !== 'string' || gettype($dataRow[1]) !== 'double') {
                        throw new \Exception('Invalid data type');
                    }
                } catch (\Exception $e) {
                    $this->errorRow++;
                    $this->errorData[] = $dataRow[0];

                    continue;
                }

                $matchProducts[0][] = (string)$dataRow[0];
                $matchProducts[1][] = (int)$dataRow[1];

                $this->insertRow++;
            } else {
                $this->skipRowData[] = (string)$dataRow[0];
                $this->skipRow++;
            }
        }

        $products = $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY)->findBy(array('name' => $matchProducts[0]));

        foreach ($products as $product) {
            $productIds[] = $product->getId();
        }

        $productCurrentQty =  $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY_QTY)->findBy(array('id' => $productIds));

        for ($i = 0, $count = count($productIds); $i < $count; $i++) {
            $productCurrentQty[$i]->setVirtualQty($matchProducts[1][$i]);
        }

        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * @param $entity
     *
     * @return array
     */
    protected function getEntityId($entity)
    {
        $items = $this->getEntityManager()->getRepository($entity)->findAll();

        $tmpArray = array();

        foreach ($items as $item) {
            $tmpArray[] = $item->getId();
        }

        return $tmpArray;
    }

    /**
     * @return array
     */
    protected function getCurrentData()
    {
        $currentData = $this->getEntityManager()->getRepository(self::PRODUCT_ENTITY)->findAll();

        $arr = array();

        for ($i = 0, $count = count($currentData); $i < $count; $i++) {
            $arr[strtolower((string)$currentData[$i]->getName())] = true;
        }

        return $arr;
    }

    /**
     * @return mixed
     */
    protected function parseExcel()
    {
        if (is_null($this->inputFileName)) {
            $this->redirect()->toRoute('fileupload');
        }

        $objReader     = PHPExcel_IOFactory::createReader($this->inputFileType);
        $objReader->setReadDataOnly(true);
        $objPHPExcel   = $objReader->load($this->inputFileName);
        $objWorksheet  = $objPHPExcel->getActiveSheet()->toArray();

        $cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize ' => '256MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        return $objWorksheet;
    }

    /**
     * @param string $path
     */
    protected function getFile($path = './data/upload/')
    {
        $file = glob($path . '*');

        $this->inputFileName = $file[0];
    }

    /**
     * @param $file
     */
    protected function fileInfo($file)
    {
        $info = pathinfo($file);

        if (strtolower($info['extension']) === 'xls') {
            $this->inputFileType = 'Excel5';
        } else {
            $this->inputFileType = 'Excel2007';
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