<?php
namespace Product\Controller;

ini_set('max_execution_time', 480);

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

    /**
     * @var
     */
    protected $em;

    protected $inputFileName;
    protected $inputFileType;

    protected $insertRow = 0;
    protected $skipRow   = 0;

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
            'skipRow'   => $this->skipRow
        ));
    }

    protected function insertData()
    {
        $currentData = $this->getCurrentData();

        $parse = $this->parseExcel();
        array_shift($parse);

        $prepareData = array();

        foreach ($parse as $dataRow) {
            $prepareData['name']      = (string)$dataRow[0];
            $prepareData['idCatalog'] = (object)$this->getEntityManager()->find(self::CATEGORY_ENTITY, $dataRow[1]);
            $prepareData['idBrand']   = (object)$this->getEntityManager()->find(self::BRAND_ENTITY, $dataRow[2]);

            if (!isset($currentData[strtolower($dataRow[0])])) {
                $product = new Product();

                $product->populate($prepareData);

                $this->getEntityManager()->persist($product);
                $this->getEntityManager()->flush();

                $this->insertRow++;
            } else {
                $this->skipRow++;
            }
        }
    }

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