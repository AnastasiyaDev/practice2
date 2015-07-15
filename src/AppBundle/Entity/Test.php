<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
 */
Class Test
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Question",mappedBy="test", cascade={"all"}, orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\ManyToMany(targetEntity="Explanation")
     * @ORM\JoinTable(name="test_explanation",
     * joinColumns={@ORM\JoinColumn(name="test_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="explanation_id",referencedColumnName="id")})
     */
    private $explanation;

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
     * Set name
     *
     * @param string $name
     * @return Test
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

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
}
