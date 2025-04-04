<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OuiController extends AbstractController
{
    #[Route('/oui', name: 'app_oui')]
    public function index(): Response
    {
        return $this->render('oui/index.html.twig', [
            'controller_name' => 'OuiController',
        ]);
    }
}
