<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Entity\Explanation;

class AnsCalculate
{
    /**
     * @param $rating
     * @param Test $test
     * @return Explanation
     */
    public function findExplanation($rating, Test $test) {
        foreach ($test->getExplanation()->getValues() as $explanation) {
            if ($rating >= $explanation->getMinRating() and $rating <= $explanation->getMaxRating()) {
                return $explanation;
            }
        }
        return null;
    }

    /**
     * @param User $user
     * @param Test $test
     * @return int
     */
    public function calculateRating(User $user, Test $test) {
        $rating = 0;
        foreach ($user->getAnswers()->getValues() as $answer) {
            if ($answer->getQuestion()->getTest()->getId()==$test->getId())
                $rating+=$answer->getRating();
        }
        return $rating;
    }

    /**
     * @param Test $test
     * @return int
     */
    public function calculateMinRating(Test $test) {
        $ratingArray = null; $rating = 0;
        foreach ($test->getQuestions()->getValues() as $question) {
            foreach ($question->getAnswers()->getValues() as $answer) {
                $ratingArray[] = $answer->getRating();
            }
            $rating += min($ratingArray);
            $ratingArray = null;
        }
        return $rating;
    }

    /**
     * @param Test $test
     * @return int
     */
    public function calculateMaxRating(Test $test) {
        $ratingArray = null; $rating = 0;
        foreach ($test->getQuestions()->getValues() as $question) {
            foreach ($question->getAnswers()->getValues() as $answer) {
                $ratingArray[] = $answer->getRating();
            }
            $rating += max($ratingArray);
            $ratingArray = null;
        }
        return $rating;
    }

    /**
     * @param Test $test
     * @param Explanation $explanation
     * @return boolean
     */
    public function checkExplanation(Test $test, Explanation $explanation) {
        if ($this->findExplanation($explanation->getMinRating(), $test) != null
            or $this->findExplanation($explanation->getMaxRating(), $test) != null) {
            return true;
        }
        return false;
    }




}