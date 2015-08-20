<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
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
        if ($this->getUser() === $this->getDoctrine()->getRepository('AppBundle:User')->findOneByRoles('ROLE_SUPER_ADMIN')) {
            $q = $this->getDoctrine()->getManager()->createQuery(
                "SELECT d FROM AppBundle:Department d WHERE d.name<>'FakeDepartment'"
            );
            $departments = $q->getResult();
        } else
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findByCompany(
            $this->getUser()->getDepartment()->getCompany()->getId());

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

        if ($this->getUser() === $this->getDoctrine()->getRepository('AppBundle:User')->findOneByRoles('ROLE_SUPER_ADMIN')) {
            $q = $this->getDoctrine()->getManager()->createQuery(
                "SELECT u FROM AppBundle:User u WHERE u.roles<>'ROLE_SUPER_ADMIN'"
            );
            $users = $q->getResult();
            return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
                'users' => $users,'back' => $this->generateUrl('adminPage')));
        } else
        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array('roles' => 'ROLE_USER',
                                                                                    'department' => $this->getUser()->getDepartment()->getId())),
                'back' => $this->generateUrl('adminPage'))
        );

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/id{id}",name="usersPageAdmin")
     */
    public function showUsersAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if ($user === $this->getDoctrine()->getRepository('AppBundle:User')->findOneByRoles('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('_welcome');

        }


        return $this->render(':users:personal_page.html.twig', array(
            'user' => $user));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/users/group{id}",name="userByGroup")
     */
    public function showUserByGroupAction($id)
    {

        return $this->render('users/admin/users.html.twig', array('user' => $this->getUser(),
            'users' => $this->getDoctrine()->getRepository('AppBundle:Department')->find($id)->getUsers(),
            'back' => $this->generateUrl('usersList')
        ));

    }

    /**
     * @Route("/users/addForm",name="addUserForm")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/{id}/edit",name="editUser")
     */
    public function editUserAction(Request $request, $id)
    {

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $em = $this->getDoctrine()->getManager();

        $user->setFirstName($request->get('_firstName'));
        $user->setSecondName($request->get('_secondName'));

        //pas
        if(!empty($request->get('_password'))) {
            $plainPassword = $request->get('_password');
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
        }

        if ($user->getDepartment() !==
            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($request->get('_department'))) {
            $this->getDoctrine()->getRepository('AppBundle:Department')->find($user->getDepartment())->removeUser($user);
            $user->setDepartment($department);
            $department->addUser($user);
        }

        $em->flush();

        return $this->redirectToRoute('usersPageAdmin',array('id' => $id));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
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
     * @Security("has_role('ROLE_SUPER_ADMIN')")
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

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/users/group{id}/del", name="delGroup")
     */
    public function delDepartmentAction($id)
    {
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);

        $department->getCompany()->removeDepartment($department);

        $em = $this->getDoctrine()->getManager();

        $em->remove($department);
        $em->flush();

        return $this->redirectToRoute('adminPage');
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/users/{id}/makeAdmin", name="makeAdmin")
     */
    public function makeAdminAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $user->setRoles('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return $this->redirectToRoute('usersPageAdmin',array('id' => $user->getId()));
    }

    /**
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Route("/users/{id}/makeUser", name="makeUser")
     */
    public function makeUserAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $user->setRoles('ROLE_USER');

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return $this->redirectToRoute('usersPageAdmin',array('id' => $user->getId()));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/id{id}/test/id{testId}/cancel", name="cancelTest")
     */
    public function cancelTestAction($id, $testId)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($testId);

        $user->removeTest($test);
        foreach ($test->getQuestions()->getValues() as $question) {
            foreach ($question->getAnswers()->getValues() as $answer) {
                $user->removeAnswer($answer);
            }
        }

        foreach ($user->getResults()->getValues() as $result) {
            if ($result->getTest() === $test) $resultUser = $result;
        }
        $user->removeResult($resultUser);

        $em = $this->getDoctrine()->getManager();

        $em->remove($resultUser);
        $em->flush();

        return $this->redirectToRoute('usersPageAdmin',array('id' => $user->getId()));
    }














}
