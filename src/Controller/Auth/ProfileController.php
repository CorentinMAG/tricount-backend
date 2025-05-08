<?php
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/api/me', name: 'me', methods: ['GET'])]
    public function index(SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $jsonUser = $serializer->serialize($user, 'json', ["groups" => ["user:read"]]);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}