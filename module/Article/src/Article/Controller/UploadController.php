<?php

namespace Article\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Article\Form;

class UploadController extends AbstractActionController
{
    protected $imageFolder = './public/img/article/';

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $form = new Form\ImgUploadForm('img-file-form');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $form->getData();

                return $this->redirect()->toRoute('admin');
            }
        }

        return new ViewModel(array(
                'form' => $form
            ));
    }

    /**
     * @return ViewModel
     */
    public function viewAction()
    {
        $tree = $this->dirToArray($this->imageFolder);

        $this->layout('layout/image');

        return new ViewModel(array(
                'result' => $tree
            ));
    }

    /**
     * @param $dir
     *
     * @return array
     */
    protected function dirToArray($dir)
    {
        $result = array();

        $scanDir = scandir($dir);

        foreach ($scanDir as $key => $value) {
            if (!in_array($value, array('.', '..'))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}