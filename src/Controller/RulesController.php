<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RulesController extends AbstractController
{
    #[Route('/rgpd', name: 'rgpd')]
    public function showRgpd(): Response
    {
        return $this->render('rules/rgpd.html.twig');
    }

    #[Route('statut', name: 'statut')]
    public function showStatut(): Response
    {
        return $this->render('rules/statut.html.twig');
    }
}
