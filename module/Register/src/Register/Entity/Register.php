<?php

namespace Register\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="register")
 */

class Register
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var date
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idStoreFrom;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idStoreTo;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idOperation;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idPaymentType;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idStatus;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $idUser;

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $idOperation
     */
    public function setIdOperation($idOperation)
    {
        $this->idOperation = $idOperation;
    }

    /**
     * @return int
     */
    public function getIdOperation()
    {
        return $this->idOperation;
    }

    /**
     * @param int $idPaymentType
     */
    public function setIdPaymentType($idPaymentType)
    {
        $this->idPaymentType = $idPaymentType;
    }

    /**
     * @return int
     */
    public function getIdPaymentType()
    {
        return $this->idPaymentType;
    }

    /**
     * @param int $idStatus
     */
    public function setIdStatus($idStatus)
    {
        $this->idStatus = $idStatus;
    }

    /**
     * @return int
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * @param int $idStoreFrom
     */
    public function setIdStoreFrom($idStoreFrom)
    {
        $this->idStoreFrom = $idStoreFrom;
    }

    /**
     * @return int
     */
    public function getIdStoreFrom()
    {
        return $this->idStoreFrom;
    }

    /**
     * @param int $idStoreTo
     */
    public function setIdStoreTo($idStoreTo)
    {
        $this->idStoreTo = $idStoreTo;
    }

    /**
     * @return int
     */
    public function getIdStoreTo()
    {
        return $this->idStoreTo;
    }

    /**
     * @param int $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }


}