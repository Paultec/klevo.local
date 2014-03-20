<?php
namespace Catalog\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog")
 */
class Category
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $idParent;

    //#############################################################################

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $idParent
     */
    public function setIdParent($idParent)
    {
        $this->idParent = (int)$idParent;
    }

    /**
     * @return mixed
     */
    public function getIdParent()
    {
        return $this->idParent;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    //#############################################################################

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}