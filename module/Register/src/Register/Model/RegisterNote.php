<?php

namespace Register\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("RegisterNote")
 */

class RegisterNote
{
    /**
     * @Annotation\Exclude()
     */
    public $id;

    /**
     * @Annotation\Type("Zend\Form\Element\Date")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Options({"label":"Дата:"})
     */
    public $date;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Готово"})
     */
    public $submit;
}