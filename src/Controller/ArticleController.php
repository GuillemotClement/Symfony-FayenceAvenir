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

class ArticleController extends AbstractController
{
    // Affichages de tous les articles
    #[Route('/article', name: 'articles_show')]
    public function index(ArticleRepository $articlesRepo): Response
    {
        $user = $this->getUser();
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
            //si on as bien une photo, on change la photo et on supprime l'ancienne
            if($picture){
                $article->setPicture($uploader->uploadArticleImage($picture, $article->getPicture()));
            }
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($user);
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article ajouté avec succès');
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
            $picture = $articleForm->get('pictureFile')->getData();
            $oldPicture = $article->getPicture();
            if($picture){
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






    
    //création d'un nouvel article
    // #[Route('/article/new', name: 'article_new')]
    // #[Route('/article/edit/{id}', name: 'article_edit')]
    // public function newArticle(Uploader $uploader, Request $request, EntityManagerInterface $em, ?int $id, ArticleRepository $articleRepo)
    // {
    //     $user = $this->getUser();

    //     // si on passe un id, on récupère article sinon on créer un nouveau
    //     if($id){
    //         $article = $articleRepo->find($id);
    //     }else{
    //         $article = new Article();
    //     }

    //     //création du formulaire
    //     $articleForm = $this->createForm(ArticleType::class, $article);
    //     //recupération de la requête
    //     $articleForm->handleRequest($request);

    //     //si formulaire est soumis et valide
    //     if($articleForm->isSubmitted() && $articleForm->isValid()){
    //         //on récupère l'image saisis par user
    //         $picture = $articleForm->get('pictureFile')->getData();
    //         //si id passé, on récupére le lien de l'ancienne picture et on ajoute en supprimant ancien
    //         if($id){
    //             $oldpicture = $article->getPicture();
    //             $article->setPicture($uploader->uploadArticleImage($picture, $oldpicture));
    //         }
    //         //utilisation du service pour l'upload de la photo
    //         $article->setPicture($uploader->uploadArticleImage($picture));
    //         //si nouvel article 
    //         if(!$id){
    //             //ajout date de création
    //             $article->setCreatedAt(new \DateTimeImmutable());
    //             //ajout d'un auteur
    //             $article->setAuthor($user);
    //         }
    //         $em->persist($article);
    //         $em->flush();
    //         // $this->addFlash('success', 'Article ajouté avec succès');
    //         return $this->redirectToRoute('articles_show');
    //     }
    //     return $this->render('article/new.html.twig',[
    //         'form' => $articleForm->createView(),
    //         'user' => $user
    //     ]);
    // }

    // //création d'un nouvel article
    // #[Route('/article/new', name: 'article_new')]
    // public function newArticle(Request $request, EntityManagerInterface $em)
    // {
    //     $user = $this->getUser();
    //     $article = new Article();
    //     $articleForm = $this->createForm(ArticleType::class, $article);
    //     $articleForm->handleRequest($request);
    //     if($articleForm->isSubmitted() && $articleForm->isValid()){
    //         // gestion envoi image
    //         $picture = $articleForm->get('pictureFile')->getData();
    //         $folder = $this->getParameter('article.folder');
    //         $ext = $picture->guessExtension();
    //         $filename = bin2hex(random_bytes(10)) . '.' . $ext;
    //         $picture->move($folder, $filename);
    //         $article->setPicture($this->getParameter('article.folder.public_path') . '/articles/' . $filename);
    //         $article->setCreatedAt(new \DateTimeImmutable());
    //         $article->setAuthor($user);
    //         $em->persist($article);
    //         $em->flush();
    //         $this->addFlash('success', 'Article ajouté avec succès');
    //         return $this->redirectToRoute('articles_show');
    //     }
    //     return $this->render('article/new.html.twig',[
    //         'form' => $articleForm->createView(),
    //         'user' => $user
    //     ]);
    // }

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

    //Afficher un article précis
    #[Route('article/show/{id}', name: 'article_show')]
    public function showArticle(ArticleRepository $articleRepo, ?int $id, ResponseRepository $commentRepo)
    {
        $user = $this->getUser();
        $article = $articleRepo->find($id);
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
        $articles = $articleRepo->getFilterArticles($category);
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'user' => $user
        ]);
    }
}
