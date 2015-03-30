<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAddition
 *
 * @ORM\Table(name="user_addition", indexes={@ORM\Index(name="idStore", columns={"idStore"}), @ORM\Index(name="idUser", columns={"idUser"})})
 * @ORM\Entity
 */
class UserAddition
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
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalBuy", type="integer", nullable=false)
     */
    private $totalBuy;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="discount", type="integer", nullable=true)
     */
    private $discount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="checked", type="boolean", nullable=false)
     */
    private $checked = '0';

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
     * Set phone
     *
     * @param string $phone
     * @return UserAddition
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set totalBuy
     *
     * @param integer $totalBuy
     * @return UserAddition
     */
    public function setTotalBuy($totalBuy)
    {
        $this->totalBuy = $totalBuy;

        return $this;
    }

    /**
     * Get totalBuy
     *
     * @return integer
     */
    public function getTotalBuy()
    {
        return $this->totalBuy;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return UserAddition
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set discount
     *
     * @param integer $discount
     * @return UserAddition
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return integer
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     * @return UserAddition
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return boolean
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set idStore
     *
     * @param \Data\Entity\Store $idStore
     * @return UserAddition
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
     * Set idUser
     *
     * @param \User\Entity\User $idUser
     * @return UserAddition
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
        $this->phone    = $data['phone'];
        $this->totalBuy = $data['totalBuy'];
        $this->idStore  = $data['idStore'];
        $this->idUser   = $data['idUser'];
        $this->address  = $data['address'];
        $this->checked  = $data['checked'];
        $this->discount = $data['discount'];
    }
}
