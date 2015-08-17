<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */
Class Test extends NamedEntity
{
    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Question",mappedBy="test", cascade={"all"}, orphanRemoval=true,fetch="LAZY")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="Explanation",mappedBy="test", cascade={"all"},orphanRemoval=true,fetch="LAZY")
     */
    private $explanation;

    /**
     * @ORM\ManyToMany(targetEntity="User",mappedBy="tests",fetch="LAZY")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Result",mappedBy="test", cascade={"all"},orphanRemoval=true)
     */
    private $results;

    /**
     * @ORM\ManyToMany(targetEntity="Company",mappedBy="tests",fetch="LAZY")
     */
    private $companies;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"all"},orphanRemoval=true,fetch="LAZY")
     * @ORM\JoinColumn(name="image_id",referencedColumnName="id",nullable=true)
     */
    private $image;


    /**
     * Set description
     *
     * @param string $description
     * @return Test
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
     * Constructor
     */
    public function __construct()
    {
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add questions
     *
     * @param \AppBundle\Entity\Question $questions
     * @return Test
     */
    public function addQuestion(\AppBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }

    /**
     * Remove questions
     *
     * @param \AppBundle\Entity\Question $questions
     */
    public function removeQuestion(\AppBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add explanation
     *
     * @param \AppBundle\Entity\Explanation $explanation
     * @return Test
     */
    public function addExplanation(\AppBundle\Entity\Explanation $explanation)
    {
        $this->explanation[] = $explanation;

        return $this;
    }

    /**
     * Remove explanation
     *
     * @param \AppBundle\Entity\Explanation $explanation
     */
    public function removeExplanation(\AppBundle\Entity\Explanation $explanation)
    {
        $this->explanation->removeElement($explanation);
    }

    /**
     * Get explanation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Test
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add results
     *
     * @param \AppBundle\Entity\Result $results
     * @return Test
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

    /**
     * Get results
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Add companies
     *
     * @param \AppBundle\Entity\Company $companies
     * @return Test
     */
    public function addCompany(\AppBundle\Entity\Company $companies)
    {
        $this->companies[] = $companies;

        return $this;
    }

    /**
     * Remove companies
     *
     * @param \AppBundle\Entity\Company $companies
     */
    public function removeCompany(\AppBundle\Entity\Company $companies)
    {
        $this->companies->removeElement($companies);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     * @return Test
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeImages()
    {
        foreach ($this->getQuestions()->getValues() as $question) {
            if (!$question->getImage() == null) {
                $question->removeImages();
            }
            else continue;
        }
        if (!$this->image == null) {
            $this->image->removeUpload();
            rmdir(__DIR__.'/../../../web/images/tests/'.$this->getId());
        }
        return;
    }
}
