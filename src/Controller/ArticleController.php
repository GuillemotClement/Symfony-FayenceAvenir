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

        // $articles = [
        //     [
        //         'title' => 'article 1',
        //         'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
        //         'author' => 'Jean Bon',
        //         'picture' => 'https://images.unsplash.com/photo-1707779734349-ef2bba17dfdb?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8MXx8fGVufDB8fHx8fA%3D%3D',
        //         'category' => 'politic',
        //         'createdAt' => 'Lundi 7 juin'
        //     ],
        //     [
        //         'title' => 'article 2',
        //         'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
        //         'author' => 'Emma Douce',
        //         'picture' => 'https://images.unsplash.com/photo-1713458159923-e511573e905c?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        //         'category' => 'food',
        //         'createdAt' => 'Lundi 7 juin'
        //     ],
        //     [
        //         'title' => 'article 3',
        //         'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
        //         'author' => 'Jean Bon',
        //         'picture' => 'https://images.unsplash.com/photo-1706530664711-ad4704cd27f1?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8Njl8fHxlbnwwfHx8fHw%3D',
        //         'category' => 'nature',
        //         'createdAt' => 'Lundi 7 juin'
        //     ],
        //     [
        //         'title' => 'article 2',
        //         'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
        //         'author' => 'Emma Douce',
        //         'picture' => 'https://images.unsplash.com/photo-1713458159923-e511573e905c?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        //         'category' => 'food',
        //         'createdAt' => 'Lundi 7 juin'
        //     ],
        //     [
        //         'title' => 'article 3',
        //         'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
        //         'author' => 'Jean Bon',
        //         'picture' => 'https://images.unsplash.com/photo-1706530664711-ad4704cd27f1?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8Njl8fHxlbnwwfHx8fHw%3D',
        //         'category' => 'nature',
        //         'createdAt' => 'Lundi 7 juin'
        //     ],
        // ];
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
}
