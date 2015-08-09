<?php
/**
 * Created by PhpStorm.
 * User: s
 * Date: 08.08.15
 * Time: 15:39
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="results")
 */
class Result
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\ManyToOne(targetEntity="test", inversedBy="results")
     * @ORM\JoinColumn(name="test_id",referencedColumnName="id")
     */
    private $test;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="results")
     */
    private $user;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="Explanation", inversedBy="results")
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
     * Set rating
     *
     * @param integer $rating
     * @return Result
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Result
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set test
     *
     * @param \AppBundle\Entity\test $test
     * @return Result
     */
    public function setTest(\AppBundle\Entity\test $test = null)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return \AppBundle\Entity\test 
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Result
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set explanation
     *
     * @param \AppBundle\Entity\Explanation $explanation
     * @return Result
     */
    public function setExplanation(\AppBundle\Entity\Explanation $explanation = null)
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Get explanation
     *
     * @return \AppBundle\Entity\Explanation 
     */
    public function getExplanation()
    {
        return $this->explanation;
    }
}
