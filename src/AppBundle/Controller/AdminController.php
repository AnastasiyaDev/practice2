<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Test;

class AdminController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin",name="adminPage")
     */
    public function indexAction()
    {

        $tests = $this->getDoctrine()->getRepository('AppBundle:Test')->findAll();

        return $this->render(':users/admin:admin_page.html.twig', array('user' => $this->getUser(),
            'tests' => $tests));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users",name="usersList")
     */
    public function showAllUsersAction() {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findByRoles('ROLE_USER');

        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $users));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/group",name="groupList")
     */
    public function showAllGroupAction() {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findByRoles('ROLE_USER');

        return $this->render(':users/admin:all_groups.html.twig', array('user' => $this->getUser(),
            'users' => $users));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/group/{groupName}",name="groupUser")
     */
    public function showUserByGroupAction($groupName) {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findByGroupName($groupName);

        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $users));

    }

    /**
     * @Route("/users/addForm",name="addUserForm")
     */
    public function addUserFormAction() {
        return $this->render(':users:registration.html.twig');
    }

    /**
     * @Route("/users/add",name="addUser")
     */
    public function addUserAction() {

    }

    /**
     * @Route("/id{id}/del",name="delUser")
     */
    public function delUserAction($id) {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->indexAction();
    }








}
