<?php

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductCurrentQty
 *
 * @ORM\Table(name="product_current_qty", indexes={@ORM\Index(name="idProduct", columns={"idProduct"})})
 * @ORM\Entity
 */
class ProductCurrentQty
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
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var integer
     *
     * @ORM\Column(name="virtualQty", type="integer", nullable=false)
     */
    private $virtualQty = '0';

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
     * @return ProductCurrentQty
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
     * Set virtualQty
     *
     * @param integer $virtualQty
     * @return ProductCurrentQty
     */
    public function setVirtualQty($virtualQty)
    {
        $this->virtualQty = $virtualQty;

        return $this;
    }

    /**
     * Get virtualQty
     *
     * @return integer
     */
    public function getVirtualQty()
    {
        return $this->virtualQty;
    }

    /**
     * Set idProduct
     *
     * @param \Product\Entity\Product $idProduct
     * @return ProductCurrentQty
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
        $this->virtualQty   = $data['virtualQty'];
        $this->idProduct    = $data['idProduct'];
    }
}