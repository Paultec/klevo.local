<?php

namespace Application\Service;

class FullNameCategoryService
{
    const CATEGORY_ENTITY = 'Catalog\Entity\Catalog';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $fullName;

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getFullNameCategory($id)
    {
        $category = $this->em->find(self::CATEGORY_ENTITY, $id);
        $fullName = $category->getName();

        if (null == $category->getIdParent()) {
            if (!$this->fullName) {
                $this->fullName = $fullName;
            }

            return $this->fullName;
        } else {
            $parent = $this->em->find(self::CATEGORY_ENTITY, $category->getIdParent());
            $parentName = $parent->getName();

            if ($this->fullName) {
                $this->fullName = $parentName . " :: " . $this->fullName;
            } else {
                $this->fullName = $parentName . " :: " . $fullName;
            }

            return $this->getFullNameCategory($parent->getId());
        }
    }

    /**
     * Установить значение в Null при необходимости (например после итерации)
     */
    public function setFullNameToNull()
    {
        $this->fullName = null;
    }

    /**
     * @param $em
     */
    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}