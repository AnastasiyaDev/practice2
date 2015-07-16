<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity)
 * @ORM\Table(name="explanations")
 */
class Explanation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

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
}
