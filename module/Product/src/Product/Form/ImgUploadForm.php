<?php
namespace Product\Form;

use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Validator\File\Extension;

class ImgUploadForm extends Form
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
                    'label' => 'Загрузить изображение',
                ),
            ));

        $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-success img-file-button',
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
                'target'          => './public/img/product/',
                'overwrite'       => true,
                'use_upload_name' => true,
                'randomize'       => true
            )
        );
        $inputFilter->add($file);

        return $inputFilter;
    }
}