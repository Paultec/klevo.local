<?php

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"description"})}, indexes={@ORM\Index(name="id_ratio_category", columns={"idCatalog", "idBrand"}), @ORM\Index(name="idBrand", columns={"idBrand"}), @ORM\Index(name="idStatus", columns={"idStatus"}), @ORM\Index(name="idSupplier", columns={"idSupplier"}), @ORM\Index(name="IDX_D34A04ADB9559B5", columns={"idCatalog"})})
 * @ORM\Entity
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="indexed", type="integer", nullable=true)
     */
    private $indexed;

    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer", nullable=true)
     */
    private $qty;

    /**
     * @var \Data\Entity\Store
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Store")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSupplier", referencedColumnName="id")
     * })
     */
    private $idSupplier;

    /**
     * @var \Data\Entity\Status
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Status")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="id")
     * })
     */
    private $idStatus;

    /**
     * @var \Catalog\Entity\Catalog
     *
     * @ORM\ManyToOne(targetEntity="Catalog\Entity\Catalog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCatalog", referencedColumnName="id")
     * })
     */
    private $idCatalog;

    /**
     * @var \Catalog\Entity\Brand
     *
     * @ORM\ManyToOne(targetEntity="Catalog\Entity\Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBrand", referencedColumnName="id")
     * })
     */
    private $idBrand;

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
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return Product
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
     * Set img
     *
     * @param string $img
     * @return Product
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set indexed
     *
     * @param integer $indexed
     * @return Product
     */
    public function setIndexed($indexed)
    {
        $this->indexed = $indexed;

        return $this;
    }

    /**
     * Get indexed
     *
     * @return integer
     */
    public function getIndexed()
    {
        return $this->indexed;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     * @return Product
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
     * Set idSupplier
     *
     * @param \Data\Entity\Store $idSupplier
     * @return Product
     */
    public function setIdSupplier(\Data\Entity\Store $idSupplier = null)
    {
        $this->idSupplier = $idSupplier;

        return $this;
    }

    /**
     * Get idSupplier
     *
     * @return \Data\Entity\Store
     */
    public function getIdSupplier()
    {
        return $this->idSupplier;
    }

    /**
     * Set idStatus
     *
     * @param \Data\Entity\Status $idStatus
     * @return Product
     */
    public function setIdStatus(\Data\Entity\Status $idStatus = null)
    {
        $this->idStatus = $idStatus;

        return $this;
    }

    /**
     * Get idStatus
     *
     * @return \Data\Entity\Status
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * Set idCatalog
     *
     * @param \Catalog\Entity\Catalog $idCatalog
     * @return Product
     */
    public function setIdCatalog(\Catalog\Entity\Catalog $idCatalog = null)
    {
        $this->idCatalog = $idCatalog;

        return $this;
    }

    /**
     * Get idCatalog
     *
     * @return \Catalog\Entity\Catalog
     */
    public function getIdCatalog()
    {
        return $this->idCatalog;
    }

    /**
     * Set idBrand
     *
     * @param \Catalog\Entity\Brand $idBrand
     * @return Product
     */
    public function setIdBrand(\Catalog\Entity\Brand $idBrand = null)
    {
        $this->idBrand = $idBrand;

        return $this;
    }

    /**
     * Get idBrand
     *
     * @return \Catalog\Entity\Brand
     */
    public function getIdBrand()
    {
        return $this->idBrand;
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
        $this->id          = $data['id'];
        $this->name        = $data['name'];
        $this->description = $data['description'];
        $this->price       = $data['price'];
        $this->img         = $data['img'];
        $this->qty         = $data['qty'];
        $this->idSupplier  = $data['idSupplier'];
        $this->idStatus    = $data['idStatus'];
        $this->idCatalog   = $data['idCatalog'];
        $this->idBrand     = $data['idBrand'];
        $this->indexed     = $data['indexed'];
    }
}