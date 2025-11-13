<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\WorksRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Works;
use App\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class WorksController extends AbstractController
{
    private $tokenStorage;

    #[Route('/works', name: 'app_works', methods:['GET'])]
    public function getWorksList(WorksRepository $worksRepository, SerializerInterface $serializer): JsonResponse
    {
        $worksList = $worksRepository->findAll();
         
        $jsonWorksList = $serializer->serialize($worksList, 'json');
        return new JsonResponse($jsonWorksList, Response::HTTP_OK, [], true);
    }

    #[Route('/works', name: 'createWorks', methods:['POST'])]
    public function createWorks(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        
        if($token){
            $works = $serializer->deserialize($request->getContent(), Works::class, 'json');
            $works->author = $serializer->deserialize($token->user, Users::class, 'json');
            $em->persist($works);
            $em->flush();

            $jsonWorks = $serializer->serialize($works, 'json');
    
            return new JsonResponse($jsonWorks, Response::HTTP_CREATED);
        }
        else{
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
    }
    #[Route('/works/{id}', name: 'deleteWorks', methods: ['DELETE'])]
    public function deleteWorks(Works $works, EntityManagerInterface $em): JsonResponse 
    {
        $token = $this->tokenStorage->getToken();
        
        if($token){
            if($works->getauthor()->getId() == $token->user->id){
                $em->remove($works);
                $em->flush();
        
                return new JsonResponse(null, Response::HTTP_NO_CONTENT);
            }
            else{
                return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
            }
        }
        else{
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
    }
}
