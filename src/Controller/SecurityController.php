<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Service\Uploader;
use App\Entity\ResetPassword;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SecurityController extends AbstractController
{
    public function __construct(private FormLoginAuthenticator $authenticator)
    {
    }
    // fonction inscription d'un nouvel utilisateur
    #[Route('/register', name: 'register')]
    public function register(Uploader $uploader, UserAuthenticatorInterface $userAuthicator, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer)
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
            // $this->addFlash('success', 'Bienvenue sur FayenceAvenir');

            // envoi du mail de bienvenue
            $email = new TemplatedEmail();
            $email->to($user->getEmail())
                ->from('contact@fayence-avenir.fr')
                ->subject('Bienvenue sur Fayence-Avenir')
                ->htmlTemplate('@email_templates/welcome.html.twig')
                ->context([
                  'username' => $user->getFirstname()
                ]);
            $mailer->send($email);

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

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword(UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $em, string $token, ResetPasswordRepository $resetPasswordRepository, RateLimiterFactory $passwordRecoveryLimiter)
    {
        $limiter = $passwordRecoveryLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            $this->addFlash('error', 'Vous devez attendre une heure pour refaire une récupération');
            return $this->redirectToRoute('login');
        }

        $resetPassword = $resetPasswordRepository->findOneBy(['token' => sha1($token)]);
        if(!$resetPassword || $resetPassword->getExpiredAt() < new \DateTime('now')){
            if($resetPassword){
                $em->remove($resetPassword);
                $em->flush();
            }
            $this->addFlash('error', 'Votre demande est expirée veuillez refaire une demande.');
            return $this->redirectToRoute('login');
        }

        $passwordForm = $this->createFormBuilder()
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe', 
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit faire au moins 6 caractères'
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner un mot de passe'
                    ])
                ]
            ])
            ->getForm();

        $passwordForm->handleRequest($request);

        if($passwordForm->isSubmitted() && $passwordForm->isValid()){
            $password = $passwordForm->get('password')->getData();
            $user = $resetPassword->getUser();
            $hash = $userPasswordHasher->hashPassword($user, $password);
            $user->setPassword($hash);
            $em->remove($resetPassword);
            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié');
            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password_form.html.twig', [
            'form' => $passwordForm->createView()
        ]);
    }

    // #[Route('/reset-password', name: 'reset_password_request')]
    // public function resetPasswordRequest(MailerInterface $mailer,Request $request, UserRepository $userRepository, ResetPasswordRepository $resetPasswordRepository, EntityManagerInterface $em)
    // {
    //     $emailForm = $this->createFormBuilder()->add('email', EmailType::class, [
    //         'constraints' => [
    //             new NotBlank([
    //                 'message' => 'Veuillez renseigner votre adresse email',
    //             ])
    //         ]
    //     ])->getForm();

    //     $emailForm->handleRequest($request);

    //     if($emailForm->isSubmitted() && $emailForm->isValid()){
    //         $emailValue = $emailForm->get('email')->getData();
    //         $user = $userRepository->findOneBy(['email' => $emailValue]);
    //         // dd($user);
    //         if($user){
    //             $oldResetPassword = $resetPasswordRepository->findOneBy(['user' => $user]);
    //             if($oldResetPassword){
    //                 $em->remove($oldResetPassword);
    //                 $em->flush();
    //             }
    //             $resetPassword = new ResetPassword();
    //             $resetPassword->setUser($user);
    //             $resetPassword->setExpiredAt(new \DateTimeImmutable('+2 hours'));
    //             $token = substr(str_replace(['=', '+', '/'], '', base64_encode(random_bytes(32))), 0, 20);
    //             $resetPassword->setToken($token);
    //             $em->persist($resetPassword);
    //             $em->flush();
    //             $email = new TemplatedEmail();
    //             $email->to($emailValue)
    //                 ->from('contact@fayence-avenir.fr')
    //                 ->subject('Reinitialisation du mot de passe')
    //                 ->htmlTemplate('@email_templates/reset_password_request.html.twig')
    //                 ->context([
    //                     'token' => $token
    //                 ]);
    //             $mailer->send($email);
    //         }
    //         $this->addFlash('success', 'Un email pour remplacer le mot de passe a été envoyé');
    //         return $this->redirectToRoute('home');
    //     }

    //     return $this->render('security/reset_password.html.twig', [
    //         'form' => $emailForm->createView()
    //     ]);
    // }
    #[Route('/reset-password-request', name: 'reset_password_request')]
    public function resetPasswordRequest(RateLimiterFactory $passwordRecoveryLimiter, MailerInterface $mailer, Request $request, UserRepository $userRepository, ResetPasswordRepository $resetPasswordRepository, EntityManagerInterface $em)
    {
        // $limiter = $passwordRecoveryLimiter->create($request->getClientIp());
        // if (false === $limiter->consume(1)->isAccepted()) {
        //     $this->addFlash('error', 'Vous devez attendre une heure pour refaire une récupération');
        //     return $this->redirectToRoute('login');
        // }

        $emailForm = $this->createFormBuilder()->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner votre email'
                ])
            ]
        ])->getForm();
        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $emailValue = $emailForm->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $emailValue]);
            if ($user) {
                $oldResetPassword = $resetPasswordRepository->findOneBy(['user' => $user]);
                if ($oldResetPassword) {
                    $em->remove($oldResetPassword);
                    $em->flush();
                }
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user);
                $resetPassword->setExpiredAt(new \DateTimeImmutable('+2 hours'));
                $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(30))), 0, 20);
                $hash = sha1($token);
                $resetPassword->setToken($hash);
                $em->persist($resetPassword);
                $em->flush();
                $email = new TemplatedEmail();
                $email->to($emailValue)
                    ->from('contact@fayence-avenir.fr')
                    ->subject('Demande de réinitialisation de mot de passe')
                    ->htmlTemplate('@email_templates/reset_password_request.html.twig')
                    ->context([
                        'token' => $token
                    ]);
                $mailer->send($email);
            }
            $this->addFlash('success', 'Un email vous a été envoyé pour réinitialiser votre mot de passe');
            return $this->redirectToRoute('home');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'form' => $emailForm->createView()
        ]);
    }
}
