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

        $q = $this->getDoctrine()->getManager()->createQuery(
            "SELECT d FROM AppBundle:Department d WHERE d.name<>'FakeDepartment'"
        );
        $departments = $q->getResult();

        return $this->render(':users/admin:all_groups.html.twig', array('user' => $this->getUser(),
            'departments' => $departments));

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
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addUserFormAction() {

        $q = $this->getDoctrine()->getManager()->createQuery(
            "SELECT d FROM AppBundle:Department d WHERE d.name<>'FakeDepartment'"
        );
        $departments = $q->getResult();

        return $this->render(':users:registration.html.twig', array('departments' => $departments));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/editForm",name="editUserForm")
     */
    public function editUserFormAction($id) {

        $user =  $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $q = $this->getDoctrine()->getManager()->createQuery(
            "SELECT d FROM AppBundle:Department d WHERE d.name<>'FakeDepartment'"
        );
        $departments = $q->getResult();

        return $this->render('users/admin/edit_user.html.twig', array('user' => $user, 'departments' => $departments));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/edit",name="editUser")
     */
    public function editUserAction(Request $request, $id) {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();

        $user->setFirstName($request->get('_firstName'));
        $user->setSecondName($request->get('_secondName'));
        if ($user->getDepartment() !==
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($request->get('_department'))) {
            $this->getDoctrine()->getRepository('AppBundle:Department')->find($user->getDepartment())->removeUser($user);
            $user->setDepartment($department);
            $department->addUser($user);
        }

        $em->flush();

        return $this->redirectToRoute('userPage',array('id' => $id));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/id{id}/del",name="delUser")
     */
    public function delUserAction($id) {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('adminPage');
    }








}
