<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Explanation;
use AppBundle\Entity\Image;
use AppBundle\Entity\Result;
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

        if ($this->isGranted('ROLE_ADMIN')) {
            $results = $this->getDoctrine()->getRepository('AppBundle:Result')->findByTest($id);
            return $this->render('tests/about_test.html.twig', array('test' => $test, 'results' => $results));
        }

        $user = $this->getUser();
        if (!$user->getTests()->isEmpty()) {
            foreach ($user->getTests()->getValues() as $userTest) {
                if ($userTest === $test)
                    return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
            }
        }


        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }

        return $this->render('tests/about_test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/test/id{id}", name="testpage")
     */
    public function showTestAction(Request $request, $id)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        $user = $this->getUser();
        if (!$user->getTests()->isEmpty()) {
            foreach ($user->getTests()->getValues() as $userTest) {
                if ($userTest === $test)
                    return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
            }
        }

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array('label' => 'Изображение:'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $quest = new Question();
            $quest->setContent($request->get('_description'));
            $quest->setTest($test);
            $test->addQuestion($quest);

            $ans = ($request->get('_answer'));

            for ($i=0; $i<count($ans);$i++) {
                if (!empty($ans[$i]['content'])) {
                    $answer = new Answer();
                    $answer->setContent($ans[$i]['content']);
                    $answer->setRating($ans[$i]['rating']);
                    $answer->setQuestion($quest);
                    $quest->addAnswer($answer);
                }else continue;
            }

            if (!$form->get('file')->isEmpty()) {
                $quest->setImage($image);
                $em->persist($test);
                $em->flush();
                $image->upload($test->getId().'/'.$quest->getId());
                $image->setPath($test->getId().'/'.$quest->getId().'/'.$form->get('file')->getData()->getClientOriginalName());
                $em->persist($image);
                $em->flush();
                return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView()));
            }

            $em->persist($test);
            $em->flush();
//            return $this->redirectToRoute('testpage', array('id' => $test->getId(), 'uploadForm' => $form->createView()));
            return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView()));
        }

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }
        return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView()));
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
        $em = $this->getDoctrine()->getManager();

        $user->addTest($test);
        foreach($request->get('_answerArray') as $answer) {
            $user->addAnswer($this->getDoctrine()->getRepository('AppBundle:Answer')->find($answer));
        }
        $result = new Result();
        $result->setTest($test);
        $result->setUser($user);
        $result->setRating($this->get('calculate')->calculateRating($user,$test));
        if (!$test->getExplanation()->isEmpty()) {
            $explanation = $this->get('calculate')->findExplanation($result->getRating(),$test);
            $result->setExplanation($explanation);
            $explanation->addResult($result);

        }
        $result->setDate(new \DateTime(date('d.m.Y')));

        $test->addResult($result);
        $user->addResult($result);

        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test", name="testNewForm")
     */
    public function newTestFormAction(Request $request)
    {

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $test = new Test();

            $test->setName($request->get('_name'));
            $test->setDescription($request->get('_description'));
            $company = $this->getDoctrine()->getRepository('AppBundle:Company')->find($request->get('_company'));
            $test->addCompany($company);
            $company->addTest($test);

            if (!$form->get('file')->isEmpty()) {
                $test->setImage($image);
                $em->persist($test);
                $em->flush();
                $image->upload($test->getId());
                $image->setPath($test->getId().'/'.$form->get('file')->getData()->getClientOriginalName());
                $em->persist($image);
                $em->flush();
                return $this->redirectToRoute('aboutTestpage', array('id' => $test->getId()));
            }

            $em->persist($test);
            $em->flush();
            return $this->redirectToRoute('aboutTestpage', array('id' => $test->getId()));
        }

        return $this->render(':tests:new_test.html.twig',array(
            'companies' => $this->getDoctrine()->getRepository('AppBundle:Company')->findAll(),
            'uploadForm' => $form->createView()
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/new", name="testNew")
     */
    public function newTestAction(Request $request) {

        $test = new Test();

        $test->setName($request->get('_name'));
        $test->setDescription($request->get('_description'));
        $company = $this->getDoctrine()->getRepository('AppBundle:Company')->find($request->get('_company'));
        $test->addCompany($company);
        $company->addTest($test);

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('aboutTestpage', array('id' => $test->getId()));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/del", name="delTest")
     */
    public function delTestAction($id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $em = $this->getDoctrine()->getManager();

        foreach ($test->getCompanies() as $company) {
            $company->removeTest($test);
            $em->persist($company);
        }

        $test->removeImages();

        $em->remove($test);
        $em->flush();

        return $this->redirectToRoute('adminPage');

    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{id}/editComplete", name="editTest")
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
     * @Route("/test/id{id}/addComplete", name="addQuestion")
     */
    public function addQuestionAction(Request $request, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $em = $this->getDoctrine()->getManager();

        $quest = new Question();
        $quest->setContent($request->get('_description'));
        $quest->setTest($test);
        $test->addQuestion($quest);

        $ans = ($request->get('_answer'));

        for ($i=0; $i<count($ans);$i++) {
            if (!empty($ans[$i]['content'])) {
                $answer = new Answer();
                $answer->setContent($ans[$i]['content']);
                $answer->setRating($ans[$i]['rating']);
                $answer->setQuestion($quest);
                $quest->addAnswer($answer);
            }else continue;
        }

        $em->flush();


        return $this->redirectToRoute('testpage', array('id' => $test->getId()));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/test/id{testId}/question{id}/edit", name="editQuestion")
     */
    public function editQuestionAction(Request $request, $testId, $id) {
        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        $question->setContent($request->get('_description'));
        $question->setTest($test);

        $em = $this->getDoctrine()->getManager();

        $ans = ($request->get('_answer'));
        $questionAns = $question->getAnswers();

        foreach($questionAns as $answer)
        for ($i=0; $i<count($ans);$i++) {
            $answer->setContent($ans[$i]['content']);
            $answer->setRating($ans[$i]['rating']);
        }

        if(count($ans)>count($questionAns)) {
            for ($i=0; $i<count($ans)-count($questionAns);$i++) {
                $answer = new Answer();
                $answer->setContent($ans[$i]['content']);
                $answer->setRating($ans[$i]['rating']);
                $answer->setQuestion($question);
                $question->addAnswer($answer);
            }
        }

        $em->persist($question);
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

        $question->removeImages();

        $em->persist($test);
        $em->remove($question);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

    /**
     * @Route("/test/id{id}/addExplanation", name="addExplanation")
     */
    public function addExplanationAction(Request $request, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $explanation = new Explanation();
        $explanation->setDescription($request->get('_description'));
        $explanation->setMinRating($request->get('_minRating'));
        $explanation->setMaxRating($request->get('_maxRating'));

        $test->addExplanation($explanation);
        $explanation->setTest($test);

        $em = $this->getDoctrine()->getManager();

        $em->persist($explanation);
        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

    /**
     * @Route("/test/id{id}/explanation{idExplanation}/edit", name="editExplanation")
     */
    public function editExplanationAction(Request $request, $id, $idExplanation) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);
        $explanation = $this->getDoctrine()->getRepository('AppBundle:Explanation')->find($idExplanation);

        $explanation->setDescription($request->get('_description'));
        $explanation->setMinRating($request->get('_minRating'));
        $explanation->setMaxRating($request->get('_maxRating'));

        $em = $this->getDoctrine()->getManager();

        $em->persist($explanation);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

    /**
     * @Route("/test/id{id}/explanation{idExplanation}/del", name="delExplanation")
     */
    public function delExplanationAction(Request $request, $id, $idExplanation) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);
        $explanation = $this->getDoctrine()->getRepository('AppBundle:Explanation')->find($idExplanation);

        $test->removeExplanation($explanation);

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->remove($explanation);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }



}
