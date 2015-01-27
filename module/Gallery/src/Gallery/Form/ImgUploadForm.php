<?php
namespace Gallery\Form;

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
            'name' => 'image',
            'attributes' => array(
                'type'     => 'file',
                'class'    => 'gallery-image-input'
            ),
            'options' => array(
                'label'    => 'Загрузить изображение (до 2 Мб)',
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'attributes' => array(
                'type'  => 'textarea',
                'class' => 'form-control comment-textarea',
                'placeholder' => 'Необязательный комментарий (до 255 символов)'
            ),
            'options' => array(
                'label' => 'Описание изображения:',
            ),
        ));

        $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-success',
                    'type'  => 'submit',
                    'value' => 'Загрузить'
                ),
            ));
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        // File Input
        $file = new InputFilter\FileInput('image');
        $file->setRequired(true);
        $file->getFilterChain()->attachByName(
            'filerenameupload',
            array(
                'target'               => './public/img/gallery/klevo',
                'overwrite'            => true,
                'use_upload_extension' => true,
                'randomize'            => true
            )
        );
        $file->getValidatorChain()
            ->attachByName('filesize',      array('max' => 2048000))
            ->attachByName('filemimetype',  array('mimeType' => 'image/png,image/jpeg'));

        $inputFilter->add($file);

        // Text Input
        $comment = new InputFilter\Input('comment');
        $comment->setRequired(false);
        $inputFilter->add(array(
            'name' => 'comment',
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'min' => 0,
                        'max' => 255,
                    ),
                ),
            ),
        ));

        $inputFilter->add($comment);

        return $inputFilter;
    }
}