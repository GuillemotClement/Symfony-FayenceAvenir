<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdministrationController extends AbstractController
{
    #[Route('/administration', name: 'administration')]
    public function index(ArticleRepository $articleRepo, EventRepository $eventRepo): Response
    {
         /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $articles = $articleRepo->findBy(['author' => $user]);
        $events = $eventRepo->findBy(['author' => $user]);
        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'articles' => $articles,
            'events' => $events
        ]);
    }
}
