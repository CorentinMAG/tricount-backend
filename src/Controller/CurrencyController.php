<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Currency;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CurrencyRepository;
use Symfony\Component\Serializer\SerializerInterface;

class CurrencyController extends AbstractController
{
    #[Route('/api/currency', name: 'create_currency', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em
        ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $file = $request->files->get('file');
        
        $currency = new Currency();

        $data = $request->request->all();
        $name = $data['name'];
        $label = $data['label'];

        $currency->setLabel($label);
        $currency->setName($name);
        $currency->setImageFile($file);
        $currency->setUri("/uploads/currency");
        $em->persist($currency);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/currency', name: 'list_currency', methods: ['GET'])]
    public function list(CurrencyRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $currencyList = $repo->findAll();
        $jsonCurrencyList = $serializer->serialize($currencyList, 'json');

        return new JsonResponse($jsonCurrencyList, Response::HTTP_OK, [], true);
    }
}