<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
		#[Route('/', name: 'home')]
		public function index(ArticleRepository $articleRepo, EventRepository $eventRepo): Response
		{
				return $this->render('home/index.html.twig', [
						'controller_name' => 'HomeController',
				]);
		}
}
