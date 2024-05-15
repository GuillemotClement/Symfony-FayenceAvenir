<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }


    #[Route('/user/edit/{id}', name: 'edit_user')]
    public function editUser(Request $request, EntityManagerInterface $em, Uploader $uploader, UserRepository $userRepo,int $id)
    {
        $user = $userRepo->find($id);
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            $picture = $userForm->get('pictureFile')->getData();
            $oldPicture = $user->getPicture();
            if($picture){
                $user->setPicture($uploader->uploadProfileImage($picture, $oldPicture));
            }
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('security/register.html.twig', [
            'form'=>$userForm->createView()
        ]);
    }
}
