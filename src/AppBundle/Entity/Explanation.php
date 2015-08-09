<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity)
 * @ORM\Table(name="explanations")
 */
class Explanation extends BaseEntity
{

    /**
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $minRating;
    /**
     * @ORM\Column(type="integer")
     */
    private $maxRating;

    /**
     * @ORM\ManyToOne(targetEntity="Test",inversedBy="explanation")
     * @ORM\JoinColumn(name="test_id",referencedColumnName="id")
     */
    private $test;

    /**
     * @ORM\OneToMany(targetEntity="Result",mappedBy="explanation", cascade={"all"},orphanRemoval=true)
     */
    private $results;

    /**
     * Set description
     *
     * @param string $description
     * @return Explanation
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
     * Set minRating
     *
     * @param integer $minRating
     * @return Explanation
     */
    public function setMinRating($minRating)
    {
        $this->minRating = $minRating;

        return $this;
    }

    /**
     * Get minRating
     *
     * @return integer 
     */
    public function getMinRating()
    {
        return $this->minRating;
    }

    /**
     * Set maxRating
     *
     * @param integer $maxRating
     * @return Explanation
     */
    public function setMaxRating($maxRating)
    {
        $this->maxRating = $maxRating;

        return $this;
    }

    /**
     * Get maxRating
     *
     * @return integer 
     */
    public function getMaxRating()
    {
        return $this->maxRating;
    }

    /**
     * Set test
     *
     * @param \AppBundle\Entity\Test $test
     * @return Explanation
     */
    public function setTest(\AppBundle\Entity\Test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return \AppBundle\Entity\Test 
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Get results
     *
     * @return \AppBundle\Entity\Result 
     */
    public function getResults()
    {
        return $this->results;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->results = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add results
     *
     * @param \AppBundle\Entity\Result $results
     * @return Explanation
     */
    public function addResult(\AppBundle\Entity\Result $results)
    {
        $this->results[] = $results;

        return $this;
    }

    /**
     * Remove results
     *
     * @param \AppBundle\Entity\Result $results
     */
    public function removeResult(\AppBundle\Entity\Result $results)
    {
        $this->results->removeElement($results);
    }
}
