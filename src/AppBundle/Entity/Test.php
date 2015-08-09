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
     * @ORM\OneToMany(targetEntity="Question",mappedBy="test", cascade={"all"}, orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="Explanation",mappedBy="test", cascade={"all"},orphanRemoval=true)
     */
    private $explanation;

    /**
     * @ORM\ManyToMany(targetEntity="User",mappedBy="tests")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Result",mappedBy="test", cascade={"all"},orphanRemoval=true)
     */
    private $results;


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
}
