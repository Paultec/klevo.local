<?php

namespace Register\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegisterTable
 *
 * @ORM\Table(name="register_table", indexes={@ORM\Index(name="idProduct", columns={"idProduct", "idRegister"}), @ORM\Index(name="idRegister", columns={"idRegister"}), @ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="idOperation", columns={"idOperation"}), @ORM\Index(name="IDX_202B4BBEC3F36F5F", columns={"idProduct"})})
 * @ORM\Entity
 */
class RegisterTable
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
     * @var \Register\Entity\Register
     *
     * @ORM\ManyToOne(targetEntity="Register\Entity\Register")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idRegister", referencedColumnName="id")
     * })
     */
    private $idRegister;

    /**
     * @var \User\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $idUser;

    /**
     * @var \Data\Entity\Operation
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idOperation", referencedColumnName="id")
     * })
     */
    private $idOperation;

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
     * @return RegisterTable
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
     * @return RegisterTable
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
     * @return RegisterTable
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
     * Set idRegister
     *
     * @param \Register\Entity\Register $idRegister
     * @return RegisterTable
     */
    public function setIdRegister(\Register\Entity\Register $idRegister = null)
    {
        $this->idRegister = $idRegister;

        return $this;
    }

    /**
     * Get idRegister
     *
     * @return \Register\Entity\Register
     */
    public function getIdRegister()
    {
        return $this->idRegister;
    }

    /**
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return RegisterTable
     */
    public function setIdUser(\User\Entity\User $idUser = null)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return \User\Entity\User
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idOperation
     *
     * @param \Data\Entity\Operation $idOperation
     * @return RegisterTable
     */
    public function setIdOperation(\Data\Entity\Operation $idOperation = null)
    {
        $this->idOperation = $idOperation;

        return $this;
    }

    /**
     * Get idOperation
     *
     * @return \Data\Entity\Operation
     */
    public function getIdOperation()
    {
        return $this->idOperation;
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
        $this->id   	   = $data['id'];
        $this->qty 	       = $data['qty'];
        $this->price       = $data['price'];
        $this->idProduct   = $data['idProduct'];
        $this->idRegister  = $data['idRegister'];
        $this->idUser      = $data['idUser'];
        $this->idOperation = $data['idOperation'];
    }
}