<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\ResponseRepository;
use App\Service\Uploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Carbon\Carbon;

class ArticleController extends AbstractController
{
    // Affichages de tous les articles du plus récent au plus ancien
    #[Route('/article', name: 'articles_show')]
    public function index(ArticleRepository $articlesRepo): Response
    {
        $user = $this->getUser();
        // requête qui permet de récupérer les article du plus récent au plus vieux
        $articles = $articlesRepo->getArticleByDescCreated();   
        
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }

    // Création d'un nouvel article
    #[Route('/article/new', name: 'article_new')]
    public function newArticle(Request $request, EntityManagerInterface $em, Uploader $uploader)
    {
        $user = $this->getUser();
        // création d'un nouvel article
        $article = new Article();
        // Création d'un nouveau formulaire
        $articleForm = $this->createForm(ArticleType::class, $article);
        //On récupère la requête
        $articleForm->handleRequest($request);
        if($articleForm->isSubmitted() && $articleForm->isValid()){
            // gestion envoi image
            //on récupère la nouvelle image du formulaire
            $picture = $articleForm->get('pictureFile')->getData();
            //si on as bien une photo, on change la photo
            if($picture){
                $article->setPicture($uploader->uploadArticleImage($picture, $article->getPicture()));
            }
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($user);
            $em->persist($article);
            $em->flush();
            // $this->addFlash('success', 'Article ajouté avec succès');
            return $this->redirectToRoute('articles_show');
        }
        return $this->render('article/new.html.twig',[
            'form' => $articleForm->createView(),
            'user' => $user
        ]);
    }

    // Edition d'un article
    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function editArticle(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $em, ?int $id, Uploader $uploader)
    {
        $user = $this->getUser();
        $article = $articleRepository->find($id);
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->handleRequest($request);
        if($articleForm->isSubmitted() && $articleForm->isValid()){
            // on récupère la picture via le formulaire
            $picture = $articleForm->get('pictureFile')->getData();
            //on récupère l'ancienne photo
            $oldPicture = $article->getPicture();
            if($picture){
                // on ajoute la nouvelle photo et on supprime l'ancienne
                $article->setPicture($uploader->uploadArticleImage($picture, $oldPicture));
            }
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('article/new.html.twig',[
            'form' => $articleForm->createView(),
            'user' => $user
        ]);
    }

    // Suppression article
    #[Route('/article/delete/{id}', name: 'article_delete')]
    public function deleteArticle(EntityManagerInterface $em, ?int $id, ArticleRepository $articleRepo)
    {
        $article = $articleRepo->find($id);
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'Votre article à bien été supprimé');
        return $this->redirectToRoute("administration");
    }

    //Afficher un article précis avec ces commentaires
    #[Route('article/show/{id}', name: 'article_show')]
    public function showArticle(ArticleRepository $articleRepo, ?int $id, ResponseRepository $commentRepo)
    {
        $user = $this->getUser();
        $article = $articleRepo->find($id);
        //on récupère les commentaires lié à l'article
        $comments = $commentRepo->findBy(['article' => $article]);
        return $this->render('article/show.html.twig',[
            'article' => $article,
            'comments' => $comments,
            'user' => $user
        ]);
    }

    //Affichage d'un article selon la category
    #[Route('article/category/{category}', name: 'article_show_category')]
    public function showArticlebyCategory(ArticleRepository $articleRepo, ?string $category)
    {
        $user = $this->getUser();
        // requête permet d'afficher les articles selon la category
        $articles = $articleRepo->getFilterArticles($category);
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }
}
