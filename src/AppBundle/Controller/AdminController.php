<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Image;
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
        $q = $this->getDoctrine()->getManager()->createQuery(
            "SELECT d FROM AppBundle:Department d WHERE d.name<>'FakeDepartment'"
        );
        $departments = $q->getResult();

        return $this->render(':users/admin:admin_page.html.twig', array('user' => $this->getUser(),
            'tests' => $this->getDoctrine()->getRepository('AppBundle:Test')->findAll(),
            'departments' => $departments
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users",name="usersList")
     */
    public function showAllUsersAction()
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findByRoles('ROLE_USER');

        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $users));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/id{id}",name="usersPageAdmin")
     */
    public function showUsersAction($id)
    {
        return $this->render(':users:personal_page.html.twig', array(
            'user' => $this->getDoctrine()->getRepository('AppBundle:User')->find($id)));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/group{id}/",name="userByGroup")
     */
    public function showUserByGroupAction($id)
    {

        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $this->getDoctrine()->getRepository('AppBundle:Department')->find($id)->getUsers()
        ));

    }

    /**
     * @Route("/users/addForm",name="addUserForm")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addUserFormAction()
    {

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
    public function editUserFormAction($id)
    {

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
    public function editUserAction(Request $request, $id)
    {

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
    public function delUserAction($id)
    {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('adminPage');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/new",name="newUser")
     */
    public function newUserAction()
    {
        return $this->render('users/admin/registration_of_users.html.twig', array(
            'departments' => $this->getDoctrine()->getRepository('AppBundle:Department')->findAll()
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/users/newGroup", name="newGroupForm")
     */
    public function newDepartmentFormAction(Request $request)
    {
        return $this->render(':users/admin:new_group.html.twig',array(
            'companies' => $this->getDoctrine()->getRepository('AppBundle:Company')->findAll()
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/users/newGroup/compete", name="newGroup")
     */
    public function newDepartmentAction(Request $request)
    {
        $department = new Department();

        $department->setName($request->get('_name'));

        $department->setCompany($this->getDoctrine()->getRepository('AppBundle:Company')->find($request->get('_company')));

        $em = $this->getDoctrine()->getManager();

        $em->persist($department);
        $em->flush();

        return $this->redirectToRoute('adminPage');
    }











}
