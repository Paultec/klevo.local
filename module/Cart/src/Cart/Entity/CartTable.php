<?php

namespace Cart\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cart_table")
 */

class CartTable
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
    protected $idCartEntity;

    /**
     * @var int
     * @ORM\Column(type="integer")

     */
    protected $idProduct;

    /**
     * @var int
     * @ORM\Column(type="integer")")
     */
    protected $qty;

    /**
     * @var int
     * @ORM\Column(type="integer")")
     */
    protected $price;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getIdCartEntity()
    {
        return $this->idCartEntity;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setIdCartEntity($idCartEntity)
    {
        $this->idCartEntity = $idCartEntity;
    }

    /**
     * @return int
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setQty($qty)
    {
        $this->qty = qty;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}