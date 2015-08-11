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
    private $departments;

    /**
     * @ORM\ManyToMany(targetEntity="Test",inversedBy="companies")
     * @ORM\JoinTable(name="company_test",
     * joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="test_id", referencedColumnName="id")})
     */
    private $tests;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->departments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add departments
     *
     * @param \AppBundle\Entity\Department $departments
     * @return Company
     */
    public function addDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \AppBundle\Entity\Department $departments
     */
    public function removeDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Add tests
     *
     * @param \AppBundle\Entity\Test $tests
     * @return Company
     */
    public function addTest(\AppBundle\Entity\Test $tests)
    {
        $this->tests[] = $tests;

        return $this;
    }

    /**
     * Remove tests
     *
     * @param \AppBundle\Entity\Test $tests
     */
    public function removeTest(\AppBundle\Entity\Test $tests)
    {
        $this->tests->removeElement($tests);
    }

    /**
     * Get tests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTests()
    {
        return $this->tests;
    }
}
