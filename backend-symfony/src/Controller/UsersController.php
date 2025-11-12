<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class UsersController extends AbstractController
{
    #[Route('/users/login', name: 'api_login_check', methods: ['POST'])]
    public function login(): Response
    {
        return new JsonResponse([
            'message' => 'Login successful',
            'path' => 'src/Controller/UsersController.php',
        ]);
    }
}