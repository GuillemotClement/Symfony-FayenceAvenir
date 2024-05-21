<?php

namespace App\Controller;


use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
// use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $formContact = $this->createForm(ContactType::class);
        $formContact->handleRequest($request);

        if($formContact->isSubmitted() && $formContact->isValid()) {
            $data = $formContact->getData();
            $emailContact = $data['email'];
            $firstName = $data['firstname'];
            $lastName = $data['lastname'];
            $message = $data['content'];

           $email = new TemplatedEmail();
           $email
                ->context(['firstName' => $firstName, 'lastName' => $lastName, 'message' => $message])
                ->from($emailContact)
                ->to('contact@avenir-fayence.fr')
                ->subject('Message de ' . $firstName . ' ' . $lastName)
                ->htmlTemplate('@email_templates/contact.html.twig')
                ->text($message);
            
            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('home');
        }

        return $this->render('contact/contactForm.html.twig', [
            'form' => $formContact->createView(),
        ]);
    }
}
