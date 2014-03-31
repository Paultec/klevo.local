<?php

namespace Register\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="register_table")
 */

class RegisterTable
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
     * @ORM\Column(type="integer")
     */
    protected $idRegister;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idProduct;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $qty;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $price;

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
     * @param int $idProduct
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
    }

    /**
     * @return int
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * @param int $idRegister
     */
    public function setIdRegister($idRegister)
    {
        $this->idRegister = $idRegister;
    }

    /**
     * @return int
     */
    public function getIdRegister()
    {
        return $this->idRegister;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }


}