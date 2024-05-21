<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Tricount;
use App\Entity\TricountLabel;
use App\Entity\Currency;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TricountRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TricountController extends AbstractController
{
    #[Route('/api/tricounts', name: 'create_tricount', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em,
        SerializerInterface $serializer
        ): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $file = $request->files->get('image');
        
        $tricount = new Tricount();

        $data = $request->request->all();
        $title = $data['title'];
        $currency_id = $data['currency'];
        $label_id = $data['label'];
        $description = $data['description'] ?? null;
        $currency = $em->getRepository(Currency::class)->find($currency_id);
        $label = $em->getRepository(TricountLabel::class)->find($label_id);

        if ($file != null) {
            $tricount->setImageFile($file);
        }
        $tricount->setTitle($title);
        $tricount->setDescription($description);
        $tricount->setOwner($user);
        $tricount->setCurrency($currency);
        $tricount->setLabel($label);
        $tricount->setUri("/uploads/tricounts");
        $tricount->setJoinUri($this->generateUrl('join_tricount', ['token' => $tricount->getToken()], UrlGeneratorInterface::ABSOLUTE_URL));
        $em->persist($tricount);
        $em->flush();

        $jsonTricount = $serializer->serialize($tricount, 'json');

        return new JsonResponse($jsonTricount, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/tricounts', name: 'list_tricounts', methods: ['GET'])]
    public function list(TricountRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $tricounts = $repo->getByUser($user);
        $jsonTricounts = $serializer->serialize($tricounts, 'json');
        return new JsonResponse($jsonTricounts, Response::HTTP_OK, [], true);
    }

    #[Route('/api/tricounts/{id}', name: 'update_tricount', methods: ['PUT'])]
    public function update(Tricount $tricount, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $jsonTricount = $serializer->serialize($tricount, 'json');
        return new JsonResponse($jsonTricount, Response::HTTP_OK, [], true);

    }

    #[Route('/api/tricounts/{id}', name: 'delete_tricount', methods: ['DELETE'])]
    public function delete(Tricount $tricount, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $em->remove($tricount);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/tricounts/join/{token}', name: 'join_tricount', methods: ['POST'])]
    public function join(string $token, TricountRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $tricount = $repo->getByToken($token);
        if ($tricount == null) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } else {
            $tricount->addUser($user);
            $em->persist($tricount);
            $em->flush();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}