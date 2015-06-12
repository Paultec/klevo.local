<?php

namespace Register\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderTable
 *
 * @ORM\Table(name="order_table", indexes={@ORM\Index(name="idProduct", columns={"idProduct", "idOrder"}), @ORM\Index(name="idOrder", columns={"idOrder"}), @ORM\Index(name="IDX_75B7FBBBC3F36F5F", columns={"idProduct"})})
 * @ORM\Entity
 */
class OrderTable
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
     * @var \Product\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Product\Entity\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProduct", referencedColumnName="id")
     * })
     */
    private $idProduct;

    /**
     * @var \Register\Entity\Order
     *
     * @ORM\ManyToOne(targetEntity="Register\Entity\Order")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idOrder", referencedColumnName="id")
     * })
     */
    private $idOrder;

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
     * @return OrderTable
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
     * @return OrderTable
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
     * Set idProduct
     *
     * @param \Product\Entity\Product $idProduct
     * @return OrderTable
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
     * Set idOrder
     *
     * @param \Register\Entity\Order $idOrder
     * @return OrderTable
     */
    public function setIdOrder(\Register\Entity\Order $idOrder = null)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return \Register\Entity\Order
     */
    public function getIdOrder()
    {
        return $this->idOrder;
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
        $this->id        = $data['id'];
        $this->qty       = $data['qty'];
        $this->price     = $data['price'];
        $this->idProduct = $data['idProduct'];
        $this->idOrder   = $data['idOrder'];
    }
}
