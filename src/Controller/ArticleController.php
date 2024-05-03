<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'article_show')]
    public function index(): Response
    {

        $articles = [
            [
                'title' => 'article 1',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
                'author' => 'Jean Bon',
                'picture' => 'https://images.unsplash.com/photo-1707779734349-ef2bba17dfdb?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8MXx8fGVufDB8fHx8fA%3D%3D',
                'category' => 'politic'
            ],
            [
                'title' => 'article 2',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
                'author' => 'Emma Douce',
                'picture' => 'https://images.unsplash.com/photo-1700937192759-2a86c88128cc?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8MTR8fHxlbnwwfHx8fHw%3D',
                'category' => 'food'
            ],
            [
                'title' => 'article 3',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus cursus tellus nisi, id porttitor nisi porttitor vitae. Nulla at viverra mi. Aliquam erat volutpat. Interdum et malesuada fames ac ante ipsum primis in faucibus. Integer elementum venenatis dignissim. Etiam viverra mauris a euismod finibus. Curabitur pharetra nisi a tortor molestie, vitae vehicula odio hendrerit. Pellentesque vestibulum odio felis, id lacinia libero varius eget. Morbi facilisis est vitae massa tristique viverra a nec purus. Vivamus iaculis lorem quis nisl ultrices, nec posuere mauris rhoncus. Curabitur id magna sapien. Nam gravida, diam facilisis consectetur egestas, sapien risus lacinia risus, et ultricies mauris sapien vitae nibh. Donec hendrerit sem vel neque interdum, a hendrerit nulla condimentum.',
                'author' => 'Jean Bon',
                'picture' => 'https://images.unsplash.com/photo-1706530664711-ad4704cd27f1?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxleHBsb3JlLWZlZWR8Njl8fHxlbnwwfHx8fHw%3D',
                'category' => 'nature'
            ],
        ];

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function newArticle()
    {

        return $this->render('article/new.html.twig');
    }
}
