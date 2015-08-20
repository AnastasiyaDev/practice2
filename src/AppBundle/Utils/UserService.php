<?php

namespace AppBundle\Utils;


class UserService
{
    /**
     * @param array
     * @return array
     */
    public function getAllUsername( $users) {
        $userArray = null;
        foreach ($users as $user) {
            $userArray[] = $user->getUsername();
        }
        return $userArray;
    }

}