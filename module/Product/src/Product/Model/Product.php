<?php

namespace Product\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("product")
 */
class Product {
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Attributes({"type":"integer"})
     */
    public $id;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":"1"}})
     * @Annotation\Options({"label":"Название: "})
     * @Annotation\Attributes({"class":"form-control name_input","placeholder":"Название"})
     * @Annotation\Filter({"name":"StringTrim"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $idCatalog;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $idBrand;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Описание: "})
     * @Annotation\Attributes({"class":"form-control","placeholder":"Описание"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"max":"255"}})
     */
    public $description;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Цена, грн.: "})
     * @Annotation\Attributes({"class":"form-control number","placeholder":"Цена"})
     */
    public $price;

//    /**
//     * @Annotation\Type("Zend\Form\Element\File")
//     * @Annotation\Options({"label":"Изображение: "})
//     * @Annotation\Attributes({"class":"form-control","placeholder":"Изображение"})
//     */
//    public $img;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     * @Annotation\Attributes({"class":"btn btn-success"})
     */
    public $submit;
}