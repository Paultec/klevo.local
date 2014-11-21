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
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type = '0';

    /**
     * @var \Data\Entity\DeliveryMethod
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\DeliveryMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deliveryMethod", referencedColumnName="id")
     * })
     */
    private $deliveryMethod;

    /**
     * @var \Data\Entity\PaymentMethod
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\PaymentMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paymentMethod", referencedColumnName="id")
     * })
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
     * Set comment
     *
     * @param string $comment
     * @return CartEntity
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set type
     *
     * @param boolean $type
     * @return CartEntity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set deliveryMethod
     *
     * @param \Data\Entity\deliveryMethod $deliveryMethod
     * @return CartEntity
     */
    public function setDeliveryMethod(\Data\Entity\deliveryMethod $deliveryMethod = null)
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

    /**
     * Get deliveryMethod
     *
     * @return \Data\Entity\deliveryMethod
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Set paymentMethod
     *
     * @param \Data\Entity\paymentMethod $paymentMethod
     * @return CartEntity
     */
    public function setPaymentMethod(\Data\Entity\paymentMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \Data\Entity\paymentMethod
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
        $this->comment          = $data['comment'];
        $this->type             = $data['type'];
        $this->deliveryMethod   = $data['deliveryMethod'];
        $this->paymentMethod    = $data['paymentMethod'];
    }
}