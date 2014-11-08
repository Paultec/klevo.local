<?php

namespace Cart\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartEntity
 *
 * @ORM\Table(name="cart_entity", indexes={@ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="deliveryMethod", columns={"deliveryMethod"}), @ORM\Index(name="paymentMethod", columns={"paymentMethod"})})
 * @ORM\Entity
 */
class CartEntity
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="deliveryMethod", type="integer", nullable=true)
     */
    private $deliveryMethod;

    /**
     * @var integer
     *
     * @ORM\Column(name="paymentMethod", type="integer", nullable=true)
     */
    private $paymentMethod;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return CartEntity
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set deliveryMethod
     *
     * @param integer $deliveryMethod
     * @return CartEntity
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

    /**
     * Get deliveryMethod
     *
     * @return integer
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Set paymentMethod
     *
     * @param integer $paymentMethod
     * @return CartEntity
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return integer
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return CartEntity
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
        $this->id               = $data['id'];
        $this->date             = $data['date'];
        $this->idUser           = $data['idUser'];
        $this->deliveryMethod   = $data['deliveryMethod'];
        $this->paymentMethod    = $data['paymentMethod'];
    }
}