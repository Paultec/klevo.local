<?php
namespace Product\Controller;

ini_set('max_execution_time', 7200);

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

use Product\Entity\Product;

use PHPExcel_IOFactory;
use PHPExcel_CachedObjectStorageFactory;
use PHPExcel_Settings;

class ParseController extends AbstractActionController
{
    const PRODUCT_ENTITY  = 'Product\Entity\Product';
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';
    const BRAND_ENTITY    = 'Catalog\Entity\Brand';
    const STORE_ENTITY    = 'Data\Entity\Store';

    /**
     * @var
     */
    protected $em;

    protected $inputFileName;
    protected $inputFileType;

    protected $insertRow = 0;
    protected $skipRow   = 0;
    protected $errorRow  = 0;
    protected $errorData = array();

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getFile();
        $this->fileInfo($this->inputFileName);

        $this->insertData();

        return new ViewModel(array(
            'insertRow' => $this->insertRow,
            'skipRow'   => $this->skipRow,
            'errorRow'  => $this->errorRow,
            'errorData' => $this->errorData
        ));
    }

    /**
     * Insert data
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

        foreach ($parse as $dataRow) {
            if (!isset($currentData[strtolower($dataRow[0])])) {
                $product = new Product();

                try {
                    // Выбросить исключение, если не числовой тип
                    if ((gettype($dataRow[2]) !== 'double' || !in_array((int)$dataRow[2], $storeIds)) ||
                        (gettype($dataRow[3]) !== 'double' || !in_array((int)$dataRow[3], $brandIds)) ||
                        (gettype($dataRow[4]) !== 'double' || !in_array((int)$dataRow[4], $categoryIds)) ||
                        gettype($dataRow[5])  !== 'double'
                    ) {
                        throw new \Exception('Invalid data type');
                    }

                    $prepareData['name']        = $dataRow[0];
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
     * @param $entity
     *
     * @return array
     */
    protected function getEntityId($entity)
    {
        $items = $this->getEntityManager()->getRepository($entity)
            ->findAll();

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