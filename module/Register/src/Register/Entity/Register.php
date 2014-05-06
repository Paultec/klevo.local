<?php

namespace Register\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Register
 *
 * @ORM\Table(name="register", indexes={@ORM\Index(name="idStoreFrom", columns={"idStoreFrom", "idStoreTo", "idOperation", "idPaymentType", "idUser"}), @ORM\Index(name="idStatus", columns={"idStatus"}), @ORM\Index(name="idStoreTo", columns={"idStoreTo"}), @ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="idOperation", columns={"idOperation"}), @ORM\Index(name="idPaymentType", columns={"idPaymentType"}), @ORM\Index(name="IDX_5FF94014A97DE62B", columns={"idStoreFrom"})})
 * @ORM\Entity
 */
class Register
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
     * @var \Data\Entity\Store
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Store")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStoreFrom", referencedColumnName="id")
     * })
     */
    private $idStoreFrom;

    /**
     * @var \Data\Entity\Store
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Store")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStoreTo", referencedColumnName="id")
     * })
     */
    private $idStoreTo;

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
     * @var \Data\Entity\Operation
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idOperation", referencedColumnName="id")
     * })
     */
    private $idOperation;

    /**
     * @var \Data\Entity\PaymentType
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\PaymentType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPaymentType", referencedColumnName="id")
     * })
     */
    private $idPaymentType;

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
     * @return Register
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
     * Set idStoreFrom
     *
     * @param \Data\Entity\Store $idStoreFrom
     * @return Register
     */
    public function setIdStoreFrom(\Data\Entity\Store $idStoreFrom = null)
    {
        $this->idStoreFrom = $idStoreFrom;

        return $this;
    }

    /**
     * Get idStoreFrom
     *
     * @return \Data\Entity\Store
     */
    public function getIdStoreFrom()
    {
        return $this->idStoreFrom;
    }

    /**
     * Set idStoreTo
     *
     * @param \Data\Entity\Store $idStoreTo
     * @return Register
     */
    public function setIdStoreTo(\Data\Entity\Store $idStoreTo = null)
    {
        $this->idStoreTo = $idStoreTo;

        return $this;
    }

    /**
     * Get idStoreTo
     *
     * @return \Data\Entity\Store
     */
    public function getIdStoreTo()
    {
        return $this->idStoreTo;
    }

    /**
     * Set idStatus
     *
     * @param \Data\Entity\Status $idStatus
     * @return Register
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
     * Set idOperation
     *
     * @param \Data\Entity\Operation $idOperation
     * @return Register
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
     * Set idPaymentType
     *
     * @param \Data\Entity\PaymentType $idPaymentType
     * @return Register
     */
    public function setIdPaymentType(\Data\Entity\PaymentType $idPaymentType = null)
    {
        $this->idPaymentType = $idPaymentType;

        return $this;
    }

    /**
     * Get idPaymentType
     *
     * @return \Data\Entity\PaymentType
     */
    public function getIdPaymentType()
    {
        return $this->idPaymentType;
    }

    /**
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return Register
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
        $this->id   	     = $data['id'];
        $this->date 	     = $data['date'];
        $this->idStoreFrom   = $data['idStoreFrom'];
        $this->idStoreTo     = $data['idStoreTo'];
        $this->idStatus      = $data['idStatus'];
        $this->idOperation   = $data['idOperation'];
        $this->idPaymentType = $data['idPaymentType'];
        $this->idUser        = $data['idUser'];
    }
}