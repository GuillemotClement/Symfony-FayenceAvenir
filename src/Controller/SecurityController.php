<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class SecurityController extends AbstractController
{
    public function __construct(private FormLoginAuthenticator $authenticator)
    {
    }
    // fonction inscription d'un nouvel utilisateur
    #[Route('/register', name: 'register')]
    public function register(Uploader $uploader, UserAuthenticatorInterface $userAuthicator, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid()){
            // récupération de l'image envoyée par l'user depuis le formulaire
            $picture = $userForm->get('pictureFile')->getData();
            // on vient utiliser le service pour la gestion de la picture
            $user->setPicture($uploader->uploadProfileImage($picture));
            //on récupère le paramètre de service
            // $folder = $this->getParameter('profile.folder');
            //on récupère l'extension du fichier
            // $ext = $picture->guessExtension();
            // on créer un nom aléatoire pour éviter les problèmes de nom
            // $filename = bin2hex(random_bytes(10)) . '.' . $ext;
            // permet de déplacer l'image obtenue avec le formulaire depuis l'espace temporaire vers le dossier défini et avec le nom aléatoire
            // $picture->move($folder, $filename);
            // permet d'enregistrer dans la BDD l'url de l'image. Correspond au dossier définis dans le paramètre, et le nom définis
            // $user->setPicture($this->getParameter('profile.folder.public_path') . '/profiles/' . $filename);
            //hachage du password
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            //envoie du mot de passe haché dans le BDD
            $user->setPassword($hash);
            // par défaut, on ajoute un rôle user 
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            // $this->addFlash('success', 'Inscription réussie');
            return $userAuthicator->authenticateUser($user, $this->authenticator, $request);
        }
        return $this->render('security/register.html.twig', [
            'form'=>$userForm->createView()
        ]);
    }

    // fonction pour la connexion d'un utilisateur
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // if($this->getUser()){
        //     return $this->redirectToRoute('home');
        // }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    // fonction pour la deconnexion 
    #[Route('/logout', name: 'logout')]
    public function logout()
    {}

}
