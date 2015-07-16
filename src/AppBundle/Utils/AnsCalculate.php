<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Test;
use AppBundle\Entity\User;
use AppBundle\Entity\Explanation;

class AnsCalculate
{

    public function calculate(User $user, Test $test) {
        $rating = 0;
        foreach ($user->getAnswers()->getValues() as $answer) {
            if ($answer->getQuestion()->getTest()->getId()==$test->getId())
                $rating+=$answer->getRating();
        }
        foreach ($test->getExplanation()->getValues() as $explanation) {
            if ($rating >= $explanation->getMinRating() and $rating <= $explanation->getMaxRating()) {
                $userExplanation = new Explanation();
                $userExplanation->setDescription($explanation->getDescription());
                $userExplanation->setMinRating($rating);
                return $userExplanation;
            }
        }
        return new Explanation();

    }


}