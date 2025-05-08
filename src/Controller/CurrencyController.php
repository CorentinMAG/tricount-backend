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
    #[Route('/api/currency', name: 'list_currency', methods: ['GET'])]
    public function list(CurrencyRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $currencyList = $repo->findAll();
        $jsonCurrencyList = $serializer->serialize($currencyList, 'json', ['groups' => ['currency:read']]);

        return new JsonResponse($jsonCurrencyList, Response::HTTP_OK, [], true);
    }
}