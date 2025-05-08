<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\TricountLabel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TricountLabelRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class LabelTricountController extends AbstractController
{
    #[Route('/api/labels/tricounts', name: 'list_label_tricounts', methods: ['GET'])]
    public function list(TricountLabelRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $labelList = $repo->findAll();
        $jsonLabelList = $serializer->serialize($labelList, 'json', ['groups' => ['tricountlabel:read']]);

        return new JsonResponse($jsonLabelList, Response::HTTP_OK, [], true);
    }
}