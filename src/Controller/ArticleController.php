<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_show')]
    public function index(ArticleRepository $articlesRepo): Response
    {
        $articles = $articlesRepo->findAll();   
        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function newArticle(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        if($articleForm->isSubmitted() && $articleForm->isValid()){
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($user);
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show');
        }
        return $this->render('article/new.html.twig',[
            'form' => $articleForm->createView()
        ]);
    }


    #[Route('/article/delete/{id}', name: 'article_delete')]
    public function deleteArticle(EntityManagerInterface $em, ?int $id, ArticleRepository $articleRepo)
    {
        $article = $articleRepo->find($id);
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'Votre article à bien été supprimé');
        return $this->redirectToRoute("administration");
    }
}
