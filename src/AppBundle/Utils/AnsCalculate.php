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
        return new Explanation();

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


}