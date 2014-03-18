<?php

namespace Data\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="store")
 */

class Store
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=1000, unique=true)
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idAttribute;



    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $idAttribute
     */
    public function setIdAttribute($idAttribute)
    {
        $this->idAttribute = $idAttribute;
    }

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->idAttribute;
    }
}