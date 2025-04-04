<?php

namespace App\Controller;

use App\Repository\PostRepository; // Assurez-vous que le repository PostRepository est importé
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(PostRepository $postRepository): Response
    {
        // Récupère tous les posts de la base de données
        $posts = $postRepository->findAll();

        // Retourne la réponse avec la variable 'posts' passée à Twig
        return $this->render('test/index.html.twig', [
            'posts' => $posts,  // Assurez-vous que 'posts' est bien passé à Twig
        ]);
    }
}
