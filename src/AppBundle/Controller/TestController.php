<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Test;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;

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

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }



//        $test = new Test();
//        $test->setName("Тип тест");
//        $test->setDescription("Это тип тест.");
//        $quest = new Question();
//        $quest->setContent("А это тип вопрос.");
//        $quest->setTest($test);
//        $quest1 = new Question();
//        $quest1->setContent("А это тип ещё вопрос.");
//        $quest1->setTest($test);
//        $test->addQuestion($quest);
//        $test->addQuestion($quest1);
//        $ans = new Answer();
//        $ans->setContent("Наверно");
//        $ans->setRating(1);
//        $ans->setQuestion($quest);
//        $ans1 = new Answer();
//        $ans1->setContent("Скорее наверно");
//        $ans1->setRating(2);
//        $ans1->setQuestion($quest);
//        $ans2 = new Answer();
//        $ans2->setContent("Наверно");
//        $ans2->setRating(1);
//        $ans2->setQuestion($quest1);
//        $ans3 = new Answer();
//        $ans3->setContent("Скорее наверно");
//        $ans3->setRating(2);
//        $ans3->setQuestion($quest1);
//        $quest->addAnswer($ans);
//        $quest->addAnswer($ans1);
//        $quest1->addAnswer($ans2);
//        $quest1->addAnswer($ans3);
//
//        $em = $this->getDoctrine()->getManager();
//
//        $em->persist($test);
//        $em->persist($quest);
//        $em->persist($quest1);
//        $em->persist($ans);
//        $em->persist($ans1);
//        $em->persist($ans2);
//        $em->persist($ans3);
//        $em->flush();

        return $this->render('about_test.html.twig', array('test' => $test));
    }

    /**
     * @Route("/test/id{id}", name="testpage")
     */
    public function showTestAction($id)
    {
        $test = $this->getDoctrine()
            ->getRepository('AppBundle:Test')
            ->find($id);

        if(!$test) {
            throw $this->createNotFoundException('No found test for id'.$id);
        }
        return $this->render('test.html.twig', array('test' => $test));
    }

}
