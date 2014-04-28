<?php

namespace Cart\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CartEntity
 *
 * @ORM\Table(name="cart_entity", indexes={@ORM\Index(name="idUser", columns={"idUser"})})
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
        $this->id     = $data['id'];
        $this->idUser = $data['idUser'];
    }
}

