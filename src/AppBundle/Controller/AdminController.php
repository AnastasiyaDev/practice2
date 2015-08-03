<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Test;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;
use Symfony\Component\Yaml\Tests\A;

class AdminController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin",name="adminPage")
     */
    public function indexAction()
    {

        $tests = $this->getDoctrine()->getRepository('AppBundle:Test')->findAll();

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findByRoles('ROLE_USER');


        return $this->render(':users/admin:admin_page.html.twig', array('user' => $this->getUser(),
            'tests' => $tests, 'users' => $users));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test", name="testNewForm")
     */
    public function newTestFormAction() {
        return $this->render(':tests:new_test.html.twig');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/new", name="testNew")
     */
    public function newTestAction(Request $request) {

        $test = new Test();

        $test->setName($request->get('_name'));
        $test->setDescription($request->get('_description'));

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('aboutTestpage', array('id' => $test->getId()));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/del", name="testDel")
     */
    public function delTestAction($id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($test);
        $em->flush();

        return $this->indexAction();

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/add", name="addQuestionForm")
     */
    public function addQuestionFormAction($id) {
        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);
        return $this->render(':tests:new_question.html.twig', array('test' => $test));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/addComplete", name="addQuestion")
     */
    public function addQuestionAction(Request $request, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $quest = new Question();
        $quest->setContent($request->get('_description'));
        $quest->setTest($test);

        $answer1 = new Answer();
        $answer1->setContent($request->get('_answer1'));
        $answer1->setRating($request->get('_answer1rating'));
        $answer1->setQuestion($quest);
        $answer2 = new Answer();
        $answer2->setContent($request->get('_answer2'));
        $answer2->setRating($request->get('_answer2rating'));
        $answer2->setQuestion($quest);

        $test->addQuestion($quest);
        $quest->addAnswer($answer1);
        $quest->addAnswer($answer2);

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->persist($quest);
        $em->persist($answer1);
        $em->persist($answer2);
        $em->flush();


        return $this->redirectToRoute('aboutTestpage', array('id' => $test->getId()));

    }



}
