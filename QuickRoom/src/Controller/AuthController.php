<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AuthController extends AbstractController
{
    // Form_login se branche sur cette route (GET pour afficher, POST pour soumettre).
    #[Route(path: '/login', name: 'app_login', methods: ['GET'])]
    public function login(): Response
    {
        // Back only: renvoie juste un 200 pour dire "route OK".
        return new Response('Login route is ready (use form_login POST on /login).');
    }

    // Nécessaire pour form_login/logout. Jamais exécutée car Symfony intercepte la route.
    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Le firewall gère, ce code n'est jamais exécuté.
    }
}
