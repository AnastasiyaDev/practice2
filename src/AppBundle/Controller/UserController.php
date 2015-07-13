<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Test;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\Explanation;

class UserController extends Controller
{
//    /**
//     * @Route("/", name="homepage")
//     */
//    public function indexAction()
//    {
//        return $this->render('welcome.html.twig',
//            array(
//                'last_username' => '',
//                'error' => '',
//            )
//        );
//    }


    /**
     * @Route("/", name="userpage")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $arrayOfAlreadyUsedIds = null; $i = 0;
        foreach ($user->getTests()->getValues() as $test) {
            $test->addExplanation($this->get('calculate')->calculate($user,$test));
            $arrayOfAlreadyUsedIds[$i++] = $test->getId();
        }

        $q = $this->getDoctrine()->getManager()->createQuery(
            'SELECT t FROM AppBundle:Test t WHERE t.id NOT IN (:test)'
        )->setParameter('test',$arrayOfAlreadyUsedIds);
        $tests = $q->getResult();

        return $this->render('users/personal_page.html.twig', array('user' => $user,
            'tests' => $tests));
    }


    /**
     * @Route("/id{id}/test/id{testId}", name="userTest")
     */
    public function showUserTestAction($id, $testId)
    {
        $user = $this->getUser();
        if ($user!= $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id)) {
            return $this->redirect($this->generateUrl('userpage'));
        }

        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($testId);
        $userExp = $this->get('calculate')->calculate($user,$test);

        return $this->render(':tests:user_test.html.twig', array('user' => $user,
            'test' => $test,'userExp' => $userExp));
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
        if($request->get('_password') != $request->get('_password-2')) {
            $error = 'Пароли не совпадают';
            return $this->render(':users:registration.html.twig', array('error' => $error));
        }
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

        $token = new UsernamePasswordToken($user, $user->getPassword(), 'database_users',$user->getRoles() );
        $this->get('security.token_storage')->setToken($token);

        return $this->indexAction();
    }



}
