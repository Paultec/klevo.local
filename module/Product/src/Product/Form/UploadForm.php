<?php
namespace Product\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Validator\File\Extension;

class UploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('enctype','multipart/form-data');
        $this->setInputFilter($this->createInputFilter());

        $this->add(array(
            'name' => 'file',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'Загрузить Excel файл',
            ),
        ));

        $this->add(array(
            'name' => 'type',
            'attributes' => array(
                'value' => 'insert',
            ),
            'type' => 'radio',
            'options' => array(
                'value_options' => array(
                    'insert' => 'Добавить товары',
                    'update' => 'Обновить к-во товаров (поставщика)',
                ),
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-success excel-file-button',
                'type'  => 'submit',
                'value' => 'Загрузить'
            ),
        ));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $file = new InputFilter\FileInput('file');
        $file->setRequired(true);
        $file->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'               => './data/upload/price',
                'overwrite'            => true,
                'use_upload_extension' => true,
                'randomize'            => true
            )
        );
        $inputFilter->add($file);

        return $inputFilter;
    }
}