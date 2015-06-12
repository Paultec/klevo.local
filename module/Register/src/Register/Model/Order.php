<?php

namespace Register\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("Order")
 */

class Order
{
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Attributes({"type":"integer"})
     */
    public $id;

    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата:"})
     * @Annotation\Attributes({"class":"form-control"})
     * @Annotation\Attributes({"min":"2014-01-01","step":"1"})
     */
    public $date;

    /**
     * @Annotation\Options({"label":"От кого: "})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $idStore;

    /**
     * @Annotation\Options({"label":"Статус: "})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $idStatus;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Готово"})
     * @Annotation\Attributes({"class":"btn btn-success"})
     */
    public $submit;
}