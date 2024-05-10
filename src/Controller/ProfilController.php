<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(UserRepository $userRepo): Response
    {
        $user = $this->getUser();
        $picture = $user->getPicture();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        return $this->render('profil/index.html.twig', [
            'picture'=>$picture,
            'firstname'=>$firstname,
            'lastname'=>$lastname
        ]);
    }
}
