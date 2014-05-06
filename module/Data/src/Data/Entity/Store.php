<?php

namespace Data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store
 *
 * @ORM\Table(name="store", indexes={@ORM\Index(name="idAttrib", columns={"idAttrib"})})
 * @ORM\Entity
 */
class Store
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
     * @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var \Data\Entity\Attribute
     *
     * @ORM\ManyToOne(targetEntity="Data\Entity\Attribute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idAttrib", referencedColumnName="id")
     * })
     */
    private $idAttrib;

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
     * @return Store
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
     * @return Store
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
     * Set idAttrib
     *
     * @param \Data\Entity\Attribute $idAttrib
     * @return Store
     */
    public function setIdAttrib(\Data\Entity\Attribute $idAttrib = null)
    {
        $this->idAttrib = $idAttrib;

        return $this;
    }

    /**
     * Get idAttrib
     *
     * @return \Data\Entity\Attribute
     */
    public function getIdAttrib()
    {
        return $this->idAttrib;
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
        $this->idAttrib    = $data['idAttrib'];
    }
}