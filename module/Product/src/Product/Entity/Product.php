<?php

namespace Product\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="id_ratio_category", columns={"idCatalog", "idBrand"}), @ORM\Index(name="idBrand", columns={"idBrand"}), @ORM\Index(name="IDX_D34A04ADB9559B5", columns={"idCatalog"})})
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
     * @ORM\Column(name="code", type="string", length=11, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=true)
     */
    private $img;

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
     * Set code
     *
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
}