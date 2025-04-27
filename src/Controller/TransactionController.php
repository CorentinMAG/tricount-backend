<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\Tricount;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TransactionRepository $transactionRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/tricounts/{id}/transactions', name: 'create_transaction', methods: ['POST'])]
    public function create(Tricount $tricount, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $content = $request->toArray();
        $transaction = new Transaction();
        $transaction->setTitle($content['title']);
        $transaction->setAmount($content['amount']);
        $transaction->setDescription($content['description'] ?? null);
        $transaction->setOwner($user);
        $transaction->setTricount($tricount);

        // Set label and type if provided
        if (isset($content['label'])) {
            $label = $this->em->getRepository(TransactionLabel::class)->find($content['label']);
            if ($label) {
                $transaction->setLabel($label);
            }
        }

        if (isset($content['type'])) {
            $type = $this->em->getRepository(TransactionType::class)->find($content['type']);
            if ($type) {
                $transaction->setType($type);
            }
        }

        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            return new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $this->em->persist($transaction);
        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize($transaction, 'json', ['groups' => ['transaction:read']]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    #[Route('/api/tricounts/{id}/transactions', name: 'list_transactions', methods: ['GET'])]
    public function list(Tricount $tricount): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $transactions = $this->transactionRepository->findBy(['tricount' => $tricount, 'isActive' => true]);
        
        return new JsonResponse(
            $this->serializer->serialize($transactions, 'json', ['groups' => ['transaction:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/transactions/{id}', name: 'update_transaction', methods: ['PUT'])]
    public function update(Transaction $transaction, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$transaction->canUserEdit($user)) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $content = $request->toArray();
        
        if (isset($content['title'])) {
            $transaction->setTitle($content['title']);
        }
        if (isset($content['amount'])) {
            $transaction->setAmount($content['amount']);
        }
        if (isset($content['description'])) {
            $transaction->setDescription($content['description']);
        }
        if (isset($content['label'])) {
            $label = $this->em->getRepository(TransactionLabel::class)->find($content['label']);
            if ($label) {
                $transaction->setLabel($label);
            }
        }
        if (isset($content['type'])) {
            $type = $this->em->getRepository(TransactionType::class)->find($content['type']);
            if ($type) {
                $transaction->setType($type);
            }
        }

        $transaction->setUpdatedAt(new \DateTime());

        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            return new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize($transaction, 'json', ['groups' => ['transaction:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/transactions/{id}', name: 'delete_transaction', methods: ['DELETE'])]
    public function delete(Transaction $transaction): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$transaction->canUserEdit($user)) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $transaction->setIsActive(false);
        $this->em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
} 