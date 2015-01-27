<?php

namespace Gallery\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gallery
 *
 * @ORM\Table(name="gallery", indexes={@ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="idStatus", columns={"idStatus"})})
 * @ORM\Entity
 */
class Gallery
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
     * @ORM\Column(name="img", type="string", length=255, nullable=false)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

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
     * Set img
     *
     * @param string $img
     * @return Gallery
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
     * Set comment
     *
     * @param string $comment
     * @return Gallery
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
     * Set date
     *
     * @param \DateTime $date
     * @return Gallery
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
     * Set idStatus
     *
     * @param \Data\Entity\Status $idStatus
     * @return Gallery
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
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return Gallery
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
        $this->id       = $data['id'];
        $this->img      = $data['img'];
        $this->comment  = $data['comment'];
        $this->date     = $data['date'];
        $this->idStatus = $data['idStatus'];
        $this->idUser   = $data['idUser'];
    }
}