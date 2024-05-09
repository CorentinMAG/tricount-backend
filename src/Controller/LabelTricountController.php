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
    #[Route('/api/labels/tricounts', name: 'label_tricount_create', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em
        ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $file = $request->files->get('file');
        
        $label = new TricountLabel();

        $data = $request->request->all();
        $name = $data['name'];

        $label->setName($name);
        $label->setImageFile($file);
        $label->setUri("/uploads/labels/tricounts");
        $em->persist($label);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    
    #[Route('/api/labels/tricounts', name: 'list_label_tricounts', methods: ['GET'])]
    public function list(TricountLabelRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $labelList = $repo->findAll();
        $jsonLabelList = $serializer->serialize($labelList, 'json');

        return new JsonResponse($jsonLabelList, Response::HTTP_OK, [], true);
    }
}