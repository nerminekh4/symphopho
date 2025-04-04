<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création de l'utilisateur
        $user = new User();

        // Si la méthode est POST, on traite le formulaire d'inscription
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            // Vérification que les mots de passe sont identiques
            if ($password !== $confirmPassword) {
                return new Response('Les mots de passe ne correspondent pas.', Response::HTTP_BAD_REQUEST);
            }

            // Assignation des données à l'entité
            $user->setEmail($email);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user, // l'entité User
                    $password // le mot de passe que l'utilisateur a saisi
                )
            );

            // Enregistrement de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Message de succès et redirection vers la page de connexion
            return new Response('<p>Inscription réussie ! <a href="/login">Connectez-vous</a></p>', Response::HTTP_OK);
        }

        // Formulaire d'inscription (HTML simple)
        return new Response(
            '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Inscription</title>
            </head>
            <body>
                <h1>Créer un compte</h1>
                <form action="/register" method="POST">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br><br>

                    <label for="password">Mot de passe:</label>
                    <input type="password" id="password" name="password" required><br><br>

                    <label for="confirm_password">Confirmer le mot de passe:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

                    <button type="submit">S\'inscrire</button>
                </form>
            </body>
            </html>'
        );
    }
}
