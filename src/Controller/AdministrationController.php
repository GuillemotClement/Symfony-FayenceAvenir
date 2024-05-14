<?php

namespace App\Controller;

use App\Form\RoleType;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdministrationController extends AbstractController
{
    #[Route('/administration', name: 'administration')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ArticleRepository $articleRepo, EventRepository $eventRepo, UserRepository $userRepo): Response
    {
         /** @var \App\Entity\User $user */
        // $user = $this->getUser();
        // $users = $userRepo->findAll();
        $articles = $articleRepo->findAll();
        $events = $eventRepo->findAll();
        return $this->render('administration/index.html.twig', [
            'articles' => $articles,
            'events' => $events,
        ]);
    }

    // route for only user writer
    // #[Route('/administration', name: 'administration')]
    // public function index(ArticleRepository $articleRepo, EventRepository $eventRepo, UserRepository $userRepo): Response
    // {
    //      /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
    //     $users = $userRepo->findAll();
    //     $articles = $articleRepo->findBy(['author' => $user]);
    //     $events = $eventRepo->findBy(['author' => $user]);
    //     return $this->render('administration/index.html.twig', [
    //         'controller_name' => 'AdministrationController',
    //         'articles' => $articles,
    //         'events' => $events,
    //         'users' => $users,
    //         'user' => $user
    //     ]);
    // }

    #[Route('administration/showUser', name: 'show_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function showUser(UserRepository $userRepo)
    {
        $users = $userRepo->findAll();

        return $this->render('administration/showUser.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('administration/userRole/{id}', name: 'add_role_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function addRole(UserRepository $userRepo, EntityManagerInterface $em, ?int $id, Request $request)
    {
        $user = $userRepo->find($id);
        $roleForm = $this->createForm(RoleType::class, $user);
        $roleForm->handleRequest($request);

        if($roleForm->isSubmitted() && $roleForm->isValid()){
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('administration');
        }
        return $this->render('administration/role.html.twig', [
            'form' => $roleForm->createView(),
            'user' => $user
        ]);
    }

    #[Route('administration/user/delete/{id}', name: 'delete_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(UserRepository $userRepository, ?int $id, EntityManagerInterface $em)
    {
        $user = $userRepository->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('administration');
    }


   
}
