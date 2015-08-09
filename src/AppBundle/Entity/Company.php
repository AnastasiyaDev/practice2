<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="companies")
 */
class Company extends NamedEntity
{
    /**
     * @ORM\OneToMany(targetEntity="Department",mappedBy="company", cascade={"all"},orphanRemoval=true)
     */
    private $departaments;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->departaments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add departaments
     *
     * @param \AppBundle\Entity\Department $departaments
     * @return Company
     */
    public function addDepartament(\AppBundle\Entity\Department $departaments)
    {
        $this->departaments[] = $departaments;

        return $this;
    }

    /**
     * Remove departaments
     *
     * @param \AppBundle\Entity\Department $departaments
     */
    public function removeDepartament(\AppBundle\Entity\Department $departaments)
    {
        $this->departaments->removeElement($departaments);
    }

    /**
     * Get departaments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartaments()
    {
        return $this->departaments;
    }
}
