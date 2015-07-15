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
            if ($rating < $explanation->getRating()) {
                $userExplanation = new Explanation();
                $userExplanation->setDescription($explanation->getDescription());
                $userExplanation->setRating($rating);
                return $userExplanation;
            }
        }
        return new Explanation();

    }


}