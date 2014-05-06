<?php

namespace Register\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="idStore", columns={"idStore"})})
 * @ORM\Entity
 */
class Payment
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
     * @ORM\Column(name="amount", type="integer", nullable=false)
     */
    private $amount;

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
     * @return Payment
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
     * Set amount
     *
     * @param integer $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set idStore
     *
     * @param \Data\Entity\Store $idStore
     * @return Payment
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
        $this->id      = $data['id'];
        $this->date    = $data['date'];
        $this->amount  = $data['amount'];
        $this->idStore = $data['idStore'];
    }
}
