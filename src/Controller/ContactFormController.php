<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactFormController extends AbstractController
{
    #[Route('/contact/form', name: 'contactForm')]
    public function getFormContact(): Response
    {
        return $this->render('contact_form/formContact.html.twig');
    }
}
