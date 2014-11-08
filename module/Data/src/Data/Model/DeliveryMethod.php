<?php

namespace Data\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("delivery-method")
 */
class DeliveryMethod
{
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
     * @Annotation\Attributes({"class":"form-control","placeholder":"Название"})
     * @Annotation\Filter({"name":"StringTrim"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     * @Annotation\Attributes({"class":"btn btn-success"})
     */
    public $submit;
}