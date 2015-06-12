<?php

namespace Register\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="idStore", columns={"idStore"}), @ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="idStatus", columns={"idStatus"})})
 * @ORM\Entity
 */
class Order
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
     * @ORM\Column(name="totalSum", type="integer", nullable=true)
     */
    private $totalSum;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isRegistered", type="boolean", nullable=false)
     */
    private $isRegistered = '0';

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
     * @var \Data\Entity\Store
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Store")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStore", referencedColumnName="id")
     * })
     */
    private $idStore;

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
     * @return Order
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
     * Set totalSum
     *
     * @param integer $totalSum
     * @return Order
     */
    public function setTotalSum($totalSum)
    {
        $this->totalSum = $totalSum;

        return $this;
    }

    /**
     * Get totalSum
     *
     * @return integer
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }

    /**
     * Set isRegistered
     *
     * @param boolean $isRegistered
     * @return Order
     */
    public function setIsRegistered($isRegistered)
    {
        $this->isRegistered = $isRegistered;

        return $this;
    }

    /**
     * Get isRegistered
     *
     * @return boolean
     */
    public function getIsRegistered()
    {
        return $this->isRegistered;
    }

    /**
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return Order
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
     * Set idStore
     *
     * @param \Data\Entity\Store $idStore
     * @return Order
     */
    public function setIdStore(\Data\Entity\Store $idStore = null)
    {
        $this->idStore = $idStore;

        return $this;
    }

    /**
     * Get idStore
     *
     * @return \Data\Entity\Store
     */
    public function getIdStore()
    {
        return $this->idStore;
    }

    /**
     * Set idStatus
     *
     * @param \Data\Entity\Status $idStatus
     * @return Order
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
        $this->date         = $data['date'];
        $this->totalSum     = $data['totalSum'];
        $this->isRegistered = $data['isRegistered'];
        $this->idUser       = $data['idUser'];
        $this->idStatus     = $data['idStatus'];
        $this->idStore      = $data['idStore'];
    }
}
