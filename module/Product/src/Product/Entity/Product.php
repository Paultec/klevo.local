<?php

namespace Product\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 */

class Product
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idCatalog;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idBrand;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $code;

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

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
     * @param int $idBrand
     */
    public function setIdBrand($idBrand)
    {
        $this->idBrand = $idBrand;
    }

    /**
     * @return int
     */
    public function getIdBrand()
    {
        return $this->idBrand;
    }

    /**
     * @param int $idCatalog
     */
    public function setIdCatalog($idCatalog)
    {
        $this->idCatalog = $idCatalog;
    }

    /**
     * @return int
     */
    public function getIdCatalog()
    {
        return $this->idCatalog;
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


}