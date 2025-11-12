<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategoriesRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CategoriesController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/categories', name: 'app_categories', methods:['GET'])]
    public function getCategoriesList(CategoriesRepository $categoriesRepository, SerializerInterface $serializer): JsonResponse
    {
        $token = $this->tokenStorage->getToken();

        $categoriesList = $categoriesRepository->findAll();
         
        $jsonCategoriesList = $serializer->serialize($categoriesList, 'json');
        return new JsonResponse($jsonCategoriesList, Response::HTTP_OK, [], true);
    }
}
