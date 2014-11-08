<?php

namespace Cart\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartTable
 *
 * @ORM\Table(name="cart_table", indexes={@ORM\Index(name="idCartEntity", columns={"idCartEntity"}), @ORM\Index(name="idProduct", columns={"idProduct"})})
 * @ORM\Entity
 */
class CartTable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer", nullable=false)
     */
    private $qty;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

    /**
     * @var \Cart\Entity\CartEntity
     *
     * @ORM\ManyToOne(targetEntity="Cart\Entity\CartEntity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCartEntity", referencedColumnName="id")
     * })
     */
    private $idCartEntity;

    /**
     * @var \Product\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Product\Entity\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProduct", referencedColumnName="id")
     * })
     */
    private $idProduct;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     * @return CartTable
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return integer
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return CartTable
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set idCartEntity
     *
     * @param \Cart\Entity\CartEntity $idCartEntity
     * @return CartTable
     */
    public function setIdCartEntity(\Cart\Entity\CartEntity $idCartEntity = null)
    {
        $this->idCartEntity = $idCartEntity;

        return $this;
    }

    /**
     * Get idCartEntity
     *
     * @return \Cart\Entity\CartEntity
     */
    public function getIdCartEntity()
    {
        return $this->idCartEntity;
    }

    /**
     * Set idProduct
     *
     * @param \Product\Entity\Product $idProduct
     * @return CartTable
     */
    public function setIdProduct(\Product\Entity\Product $idProduct = null)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return \Product\Entity\Product
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        $this->id           = $data['id'];
        $this->qty          = $data['qty'];
        $this->price        = $data['price'];
        $this->idCartEntity = $data['idCartEntity'];
        $this->idProduct    = $data['idProduct'];
    }
}