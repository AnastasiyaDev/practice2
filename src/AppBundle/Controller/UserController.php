<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('welcome.html.twig',
            array(
                'last_username' => '',
                'error' => '',
            )
        );
    }

    /**
     * @Route("/id{id}", name="userpage")
     */
    public function showUserAction($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        if(!$user) {
            throw $this->createNotFoundException('No found user for id'.$id);
        }
        return $this->render('personal_page.html.twig', array('user' => $user));
    }

}
