<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\WorksRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Works;
use App\Entity\Categories;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class WorksController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/works', name: 'app_works', methods:['GET'])]
    public function getWorksList(WorksRepository $worksRepository, SerializerInterface $serializer): JsonResponse
    {
        $worksList = $worksRepository->findAll();
         
        $jsonWorksList = $serializer->serialize($worksList, 'json');
        return new JsonResponse($jsonWorksList, Response::HTTP_OK, [], true);
    }

    #[Route('/works', name: 'createWorks', methods:['POST'])]
    public function createWorks(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, SluggerInterface $slugger): JsonResponse
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !$token->getUser()) {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        // support JSON body and multipart/form-data (file upload)
        try {
            $data = $request->toArray();
        } catch (\Throwable $e) {
            $data = $request->request->all();
        }

        $title = $data['title'] ?? null;
        $categoryId = $data['category'] ?? null;

        if (!$title || !$categoryId) {
            return new JsonResponse(['error' => 'Missing title or category id'], Response::HTTP_BAD_REQUEST);
        }

        $category = $em->getRepository(Categories::class)->find($categoryId);
        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], Response::HTTP_BAD_REQUEST);
        }

        // handle uploaded image file (field name: "image")
        $imageUrl = null;
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('image');
        if ($uploadedFile instanceof UploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            $uploadsDir = $this->getParameter('kernel.project_dir').'/public/images';
            try {
                $uploadedFile->move($uploadsDir, $newFilename);
                $imageUrl = 'http://localhost:8080/images/'.$newFilename;
            } catch (\Throwable $e) {
                return new JsonResponse(['error' => 'Failed to move uploaded file'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $works = new Works();
        $works->setTitle($title);
        $works->setCategory($category);
        if ($imageUrl !== null && $imageUrl !=='') {
            $works->setImageUrl($imageUrl);
        }
        $works->setauthor($token->getUser());

        $em->persist($works);
        $em->flush();

        $jsonWorks = $serializer->serialize($works, 'json');

        return new JsonResponse($jsonWorks, Response::HTTP_CREATED, [], true);
    }

    #[Route('/works/{id}', name: 'deleteWorks', methods: ['DELETE'])]
    public function deleteWorks(Works $works, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse 
    {
        $token = $this->tokenStorage->getToken();
        
        if($token){
            //$this->logger->info($serializer->serialize($token->getUser(), 'json'));
            if($works->getauthor()->getId() == $token->getUser()->getId()){
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
