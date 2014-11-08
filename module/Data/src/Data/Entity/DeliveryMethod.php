<?php

namespace Data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryMethod
 *
 * @ORM\Table(name="delivery_method", indexes={@ORM\Index(name="idStatus", columns={"idStatus"})})
 * @ORM\Entity
 */
class DeliveryMethod
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
     * Set name
     *
     * @param string $name
     * @return DeliveryMethod
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
     * Set idStatus
     *
     * @param \Data\Entity\Status $idStatus
     * @return DeliveryMethod
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
        $this->id       = $data['id'];
        $this->name     = $data['name'];
        $this->idStatus = $data['idStatus'];
    }
}