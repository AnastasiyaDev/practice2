<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Test;

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
//        $user = new User();
//        $user->setUsername('Test');
//        //pas
//        $plainPassword = 'test';
//        $encoder = $this->container->get('security.password_encoder');
//        $encoded = $encoder->encodePassword($user, $plainPassword);
//        $user->setPassword($encoded);
//        $user->setFirstName('Тест');
//        $user->setSecondName('Тестов');
//        $user->setGroupName('тестебо3-9');
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($user);
//        $em->flush();
//
//        $user->addTest($this->getDoctrine()->getRepository('AppBundle:Test')->find(1));
        $tests = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->findAll();

        return $this->render('personal_page.html.twig', array('user' => $user,
            'tests' => $tests));
    }

}
