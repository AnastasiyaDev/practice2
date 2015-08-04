<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Test;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TestController extends Controller
{
    /**
     * @Route("/test/id{id}/about", name="aboutTestpage")
     */
    public function indexAction($id)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        $user = $this->getUser();
        foreach ($user->getTests()->getValues() as $userTest) {
            if ($userTest === $test)
                return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
        }

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }

        return $this->render('tests/about_test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/test/id{id}", name="testpage")
     */
    public function showTestAction($id)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        $user = $this->getUser();
        foreach ($user->getTests()->getValues() as $userTest) {
            if ($userTest === $test)
                return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
        }

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }
        return $this->render('tests/test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/test/id{id}/complete", name="testCompete")
     */
    public function completeTestAction($id, Request $request)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        $user = $this->getUser();

        $user->addTest($test);
        foreach($request->get('_answerArray') as $answer) {
            $user->addAnswer($this->getDoctrine()->getRepository('AppBundle:Answer')->find($answer));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

//        return $this->render('tests/test.html.twig', array('test' => $test, 'answers' => $answerArray));
        return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
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
     * @Route("/test/id{id}/edit", name="testEditForm")
     */
    public function editTestFormAction($id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        return $this->render('tests/edit_test.html.twig', array('test' => $test));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/editComplete", name="testEdit")
     */
    public function editTestAction(Request $request, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $test->setName($request->get('_name'));
        $test->setDescription($request->get('_description'));

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));

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


        return $this->redirectToRoute('testpage', array('id' => $test->getId()));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{testId}/question{id}/del", name="delQuestion")
     */
    public function delQuestionAction($testId, $id) {
        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        $test->removeQuestion($question);

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->remove($question);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

}
