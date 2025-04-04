<?php
// src/Controller/PostController.php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class PostController
{
    #[Route('/posts', name: 'posts_list')]
    public function index(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser(); // Récupère l'utilisateur connecté
        $username = $user ? $user->getUserIdentifier() : 'Invité';

        $posts = $em->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC'], 10);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'username' => $username,
        ]);

        $html = '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Liste des Posts</title>
            </head>
            <body>
                <h1>Bienvenue ' . $username . '</h1>
                <div id="posts-container">';

        foreach ($posts as $post) {
            $html .= '<div class="post">
                        <h2>' . $post->getDescription() . '</h2>
                        <p>' . $post->getCreatedAt()->format('d/m/Y H:i') . '</p>
                        <img src="/uploads/images/' . $post->getImage() . '" alt="Image du post" />
                      </div>';
        }

        $html .= '</div>
                  <script src="/js/scroll.js"></script>
            </body>
            </html>';

        return new Response($html);
    }

    #[Route('/posts/load-more', name: 'load_more_posts')]
    public function loadMorePosts(EntityManagerInterface $em, int $page): JsonResponse
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $posts = $em->getRepository(Post::class)->findBy([], ['createdAt' => 'DESC'], $limit, $offset);

        $postsData = [];
        foreach ($posts as $post) {
            $postsData[] = [
                'description' => $post->getDescription(),
                'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i'),
                'image' => '/uploads/images/' . $post->getImage()
            ];
        }

        return new JsonResponse(['posts' => $postsData]);
    }
    // src/Controller/PostController.php

#[Route('/post/new', name: 'new_post')]
public function newPost(Request $request, EntityManagerInterface $em): Response
{
    $post = new Post();
    if ($request->isMethod('POST')) {
        $description = $request->request->get('description');
        $file = $request->files->get('image');

        if ($file) {
            $imageFilename = uniqid() . '.' . $file->guessExtension();
            $file->move($this->getParameter('uploads_directory'), $imageFilename);
            $post->setImage($imageFilename);
        }

        $post->setDescription($description);
        $post->setCreatedAt(new \DateTime());
        $post->setUser($this->getUser());

        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('posts_list');
    }

    return $this->render('post/new.html.twig');
}
}
