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
     * @Route("/testid{id}", name="aboutTestpage")
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
     * @Route("/testid{id}/test", name="testpage")
     */
    public function showTestAction(Request $request, $id)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        //for complete test
        if(!$this->isGranted("ROLE_ADMIN")) {
            $user = $this->getUser();
            if (!$user->getTests()->isEmpty()) {
                foreach ($user->getTests()->getValues() as $userTest) {
                    if ($userTest === $test)
                        return $this->redirectToRoute('userTest', array('id' => $user->getId(), 'testId' => $test->getId()));
                }
            }
            return $this->render('tests/test.html.twig', array('test' => $test));
        }

        //for admin edit
        $testForm = $this->createFormBuilder($test)
            ->add('companies','entity',array('class' => 'AppBundle\Entity\Company',
                'choice_label' => 'name', 'multiple' => 'true', 'required' => false,
                'label' => ' ', 'expanded' => 'true'
            ))->getForm();

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array('label' => 'Изображение:','required' => false))
            ->getForm();


        $testForm->handleRequest($request);

        if ($testForm->isValid()) {
            $test->setName($request->get('_name'));
            $test->setDescription($request->get('_description'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();

            return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView(),
                'minRating' => $this->get('calculate')->calculateMinRating($test),
                'maxRating' => $this->get('calculate')->calculateMaxRating($test),
                'testForm' => $testForm->createView(),
            ));
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $question = new Question();
            $question->setContent($request->get('_description'));
            $question->setTest($test);
            $test->addQuestion($question);

            $ans = ($request->get('_answer'));

            for ($i=0; $i<count($ans);$i++) {
                if (!empty($ans[$i]['content'])) {
                    $answer = new Answer();
                    $answer->setContent($ans[$i]['content']);
                    $answer->setRating($ans[$i]['rating']);
                    $answer->setQuestion($question);
                    $question->addAnswer($answer);
                }else continue;
            }

            if (!$form->get('file')->isEmpty()) {
                $question->setImage($image);
                $em->persist($test);
                $em->flush();
                $image->upload($test->getId().'/'.$question->getId());
                $image->setPath($test->getId().'/'.$question->getId().'/'.$form->get('file')->getData()->getClientOriginalName());
                $em->persist($image);
                $em->flush();
                $minRating = $this->get('calculate')->calculateMinRating($test);
                $maxRating = $this->get('calculate')->calculateMaxRating($test);
                return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView(),
                    'minRating' => $this->get('calculate')->calculateMinRating($test),
                    'maxRating' => $this->get('calculate')->calculateMaxRating($test),
                    'testForm' => $testForm->createView(),
                ));
            }

            $em->persist($test);
            $em->flush();
            $minRating = $this->get('calculate')->calculateMinRating($test);
            $maxRating = $this->get('calculate')->calculateMaxRating($test);
            return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView(),
                'minRating' => $this->get('calculate')->calculateMinRating($test),
                'maxRating' => $this->get('calculate')->calculateMaxRating($test),
                'testForm' => $testForm->createView(),
            ));
        }

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }

        return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView(),
            'minRating' => $this->get('calculate')->calculateMinRating($test),
            'maxRating' => $this->get('calculate')->calculateMaxRating($test),
            'testForm' => $testForm->createView(),
        ));
    }

    /**
     * @Route("/testid{id}/test/complete", name="testCompete")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/test", name="testNewForm")
     */
    public function newTestFormAction(Request $request)
    {

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array('label' => 'Изображение:','required' => false))
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
        ));
    }


    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{id}/del", name="delTest")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{id}/test/edit", name="editTest")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{testId}/test/question{id}", name="editQuestion")
     */
    public function editQuestionAction(Request $request, $testId, $id) {
        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        $image = new Image();
        $form = $this->createFormBuilder($image)
            ->add('file','file',array('label' => 'Новое изображение:','required' => false))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $question->setContent($request->get('_description'));


            $ans = ($request->get('_answer'));
            $questionAns = $question->getAnswers()->toArray();

            $counter = 0;
            foreach($questionAns as $answer) {
                if (!empty($ans[$counter]['content'])) {
                    $answer->setContent($ans[$counter]['content']);
                    $answer->setRating($ans[$counter]['rating']);
                    $em->persist($answer);
                    $counter++;
                }else continue;
            }


            if(count($ans)>count($questionAns)) {

                for ($i=$counter; $i<count($ans);$i++) {
                    if (!empty($ans[$i]['content'])) {
                        $answer = new Answer();
                        $answer->setContent($ans[$i]['content']);
                        $answer->setRating($ans[$i]['rating']);
                        $answer->setQuestion($question);
                        $question->addAnswer($answer);
                    }else continue;
                }

            }

            if (!$form->get('file')->isEmpty()) {
                $question->removeImages();
                $question->setImage($image);
                $em->persist($test);
                $em->flush();
                $image->upload($test->getId().'/'.$question->getId());
                $image->setPath($test->getId().'/'.$question->getId().'/'.$form->get('file')->getData()->getClientOriginalName());
                $em->persist($image);
                $em->flush();
                $minRating = $this->get('calculate')->calculateMinRating($test);
                $maxRating = $this->get('calculate')->calculateMaxRating($test);
                return $this->redirectToRoute('testpage',array('id' => $testId));
            }

            $em->flush();
            return $this->redirectToRoute('testpage',array('id' => $testId));
        }

        $minRating = $this->get('calculate')->calculateMinRating($test);
        $maxRating = $this->get('calculate')->calculateMaxRating($test);


        return $this->render('tests/test.html.twig', array('test' => $test, 'uploadForm' => $form->createView(),
            'maxRating' => $maxRating,'minRating' => $minRating, 'questionEdit' => $question));

    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/test/question{qId}/answer{id}/del", name="delAnswer")
     */
    public function delAnswerAction($qId ,$id) {
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($qId);

        $answer = $this->getDoctrine()->getRepository('AppBundle:Answer')->find($id);

        $question->removeAnswer($answer);

        $em = $this->getDoctrine()->getManager();

        $em->remove($answer);
        $em->flush();

        return $this->redirectToRoute('editQuestion', array(
            'testId' => $question->getTest()->getId(), 'id' => $question->getId()
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{testId}/test/question{id}/del", name="delQuestion")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{id}/test/addExplanation", name="addExplanation")
     */
    public function addExplanationAction(Request $request, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($id);

        $explanation = new Explanation();
        $explanation->setDescription($request->get('_description'));
        $explanation->setMinRating($request->get('_minRating'));
        $explanation->setMaxRating($request->get('_maxRating'));

        if ($this->get('calculate')->checkExplanation($test, $explanation)) {
            return $this->redirectToRoute('testpage', array('id' => $test->getId()));
        }

        $test->addExplanation($explanation);
        $explanation->setTest($test);

        $em = $this->getDoctrine()->getManager();

        $em->persist($explanation);
        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{testId}/test/explanation{id}", name="editExplanationForm")
     */
    public function editExplanationFormAction(Request $request, $testId, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $explanation = $this->getDoctrine()->getRepository('AppBundle:Explanation')->find($id);

        $minRating = $this->get('calculate')->calculateMinRating($test);
        $maxRating = $this->get('calculate')->calculateMaxRating($test);

        return $this->render('tests/test.html.twig', array('test' => $test,
            'maxRating' => $maxRating,'minRating' => $minRating, 'explanationEdit' => $explanation));

    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{testId}/test/explanation{id}/edit", name="editExplanation")
     */
    public function editExplanationAction(Request $request, $testId, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $explanation = $this->getDoctrine()->getRepository('AppBundle:Explanation')->find($id);

        $explanation->setDescription($request->get('_description'));
        $explanation->setMinRating($request->get('_minRating'));
        $explanation->setMaxRating($request->get('_maxRating'));

        $em = $this->getDoctrine()->getManager();

        $em->persist($explanation);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/testid{testId}/test/explanation{id}/del", name="delExplanation")
     */
    public function delExplanationAction(Request $request, $testId, $id) {

        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);
        $explanation = $this->getDoctrine()->getRepository('AppBundle:Explanation')->find($id);

        $test->removeExplanation($explanation);

        $em = $this->getDoctrine()->getManager();

        $em->persist($test);
        $em->remove($explanation);
        $em->flush();

        return $this->redirectToRoute('testpage', array('id' => $test->getId()));
    }



}
