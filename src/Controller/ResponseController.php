<?php

namespace App\Controller;

use App\Entity\Response;
use App\Form\ResponseType;
use App\Repository\ArticleRepository;
use App\Repository\ResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResponseController extends AbstractController
{
    #[Route('/response', name: 'app_response')]
    public function index()
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'ResponseController',
        ]);
    }

    // Formulaire ajout d'un commentaire
    #[Route('/response/{id}/add', name: 'add_response')]
    public function addResponse(EntityManagerInterface $em, Request $request, ArticleRepository $articleRepo, ?int $id)
    {
        $user = $this->getUser();
        $article = $articleRepo->find($id);
        $response = new Response();
        $responseForm = $this->createForm(ResponseType::class, $response);
        $responseForm->handleRequest($request);
        if($responseForm->isSubmitted() && $responseForm->isValid()){
            $response->setCreatedAt(new \DateTimeImmutable());
            $response->setArticle($article);
            $response->setAuthor($user);
            $em->persist($response);
            $em->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succes');
            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }
        return $this->render('comment/add.html.twig', [
            'form' => $responseForm->createView(),
            'user' => $user
        ]);
    }
}
