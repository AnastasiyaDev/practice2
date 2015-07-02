<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('users/personal_page.html.twig', array('user' => $user,
            'tests' => $tests));
    }

    /**
     * @Route("/registration", name="registrationPage")
     */
    public function registrationFormAction()
    {
        return $this->render(':users:registration.html.twig');

    }

    /**
     * @Route("/registration/new", name="registrationNew")
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $user->setUsername(mb_strtolower($request->get('_username')));
        //pas
        $plainPassword = $request->get('_password');
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
        $user->setFirstName($request->get('_firstName'));
        $user->setSecondName($request->get('_secondName'));
        $user->setGroupName($request->get('_groupName'));

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        return $this->showUserAction($user->getId());
    }



}
