<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\Tricount;
use App\Entity\TricountLabel;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\TransactionSplit;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

#[Route('/api/tricounts/{tricount}/transactions')]
class TransactionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TransactionRepository $transactionRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'create_transaction', methods: ['POST'])]
    public function create(
        Tricount $tricount,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $jsondata = $request->request->get('data');
        $data = json_decode($jsondata, true);
        
        $transaction = new Transaction();
        $transaction->setTitle($data['title'] ?? '');
        $transaction->setDescription($data['description'] ?? null);
        $transaction->setAmount($data['amount'] ?? 0);
        #$transaction->setType($data['type'] ?? 'expense');
        $transaction->setOwner($user);
        $transaction->setTricount($tricount);
        
        $label = $em->getRepository(TricountLabel::class)->find($data['label']);
        if ($label) {
            $transaction->setLabel($label);
        }

        // Validate transaction
        $errors = $validator->validate($transaction);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Create splits for each user
        if (isset($data['splits']) && is_array($data['splits'])) {
            foreach ($data['splits'] as $splitData) {
                $splitUser = $em->getRepository(User::class)->find($splitData['user_id']);
                if (!$splitUser || !$tricount->canUserAccess($splitUser)) {
                    continue;
                }

                $split = new TransactionSplit();
                $split->setTransaction($transaction);
                $split->setUser($splitUser);
                $split->setAmount($splitData['amount'] ?? 0);
                $split->setIsPaid($splitData['is_paid'] ?? false);

                $transaction->addSplit($split);
            }
        }

        // Validate that splits total matches transaction amount
        if (!$transaction->validateSplits()) {
            return new JsonResponse([
                'message' => 'The sum of splits must equal the transaction amount'
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($transaction);
        $em->flush();

        $jsonTransaction = $serializer->serialize($transaction, 'json', ['groups' => ['transaction:read', 'tricountlabel:read', 'user:read', 'split:read']]);
        return new JsonResponse($jsonTransaction, Response::HTTP_CREATED, [], true);
    }

    #[Route('', name: 'list_transactions', methods: ['GET'])]
    public function list(
        Tricount $tricount,
        TransactionRepository $repo,
        SerializerInterface $serializer,
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $filters = [
            'user' => $user,
            'dateFrom' => $request->query->get('dateFrom'),
            'dateTo' => $request->query->get('dateTo'),
            'isActive' => $request->query->get('isActive', true)
        ];

        $transactions = $repo->findByTricount($tricount, $filters);
        $jsonTransactions = $serializer->serialize($transactions, 'json', ['groups' => ['transaction:read', 'user:read', 'split:read']]);
        return new JsonResponse($jsonTransactions, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'get_transaction', methods: ['GET'])]
    public function get(
        Tricount $tricount,
        Transaction $transaction,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user) || $transaction->getTricount() !== $tricount) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $jsonTransaction = $serializer->serialize($transaction, 'json', ['groups' => ['transaction:read']]);
        return new JsonResponse($jsonTransaction, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'update_transaction', methods: ['PUT'])]
    public function update(
        Tricount $tricount,
        Transaction $transaction,
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user) || $transaction->getTricount() !== $tricount) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        if (!$transaction->canUserEdit($user)) {
            return new JsonResponse(['message' => 'You can only edit your own transactions'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $transaction->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $transaction->setDescription($data['description']);
        }
        if (isset($data['amount'])) {
            $transaction->setAmount($data['amount']);
        }
        if (isset($data['type'])) {
            $transaction->setType($data['type']);
        }

        // Update splits if provided
        if (isset($data['splits']) && is_array($data['splits'])) {
            // Remove existing splits
            foreach ($transaction->getSplits() as $split) {
                $transaction->removeSplit($split);
                $em->remove($split);
            }

            // Add new splits
            foreach ($data['splits'] as $splitData) {
                $splitUser = $em->getRepository(User::class)->find($splitData['user_id']);
                if (!$splitUser || !$tricount->canUserAccess($splitUser)) {
                    continue;
                }

                $split = new TransactionSplit();
                $split->setTransaction($transaction);
                $split->setUser($splitUser);
                $split->setAmount($splitData['amount'] ?? 0);
                $split->setIsPaid($splitData['is_paid'] ?? false);

                $transaction->addSplit($split);
            }
        }

        // Validate transaction
        $errors = $validator->validate($transaction);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Validate that splits total matches transaction amount
        if (!$transaction->validateSplits()) {
            return new JsonResponse([
                'message' => 'The sum of splits must equal the transaction amount'
            ], Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        $jsonTransaction = $serializer->serialize($transaction, 'json', ['groups' => ['transaction:read']]);
        return new JsonResponse($jsonTransaction, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'delete_transaction', methods: ['DELETE'])]
    public function delete(
        Tricount $tricount,
        Transaction $transaction,
        EntityManagerInterface $em
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user) || $transaction->getTricount() !== $tricount) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        if (!$transaction->canUserEdit($user)) {
            return new JsonResponse(['message' => 'You can only delete your own transactions'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($transaction);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/splits/{splitId}/mark-paid', name: 'mark_split_paid', methods: ['POST'])]
    public function markSplitPaid(
        Tricount $tricount,
        Transaction $transaction,
        int $splitId,
        EntityManagerInterface $em
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user) || $transaction->getTricount() !== $tricount) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $split = $transaction->getSplits()->filter(function(TransactionSplit $split) use ($splitId) {
            return $split->getId() === $splitId;
        })->first();

        if (!$split) {
            return new JsonResponse(['message' => 'Split not found'], Response::HTTP_NOT_FOUND);
        }

        $split->setIsPaid(true);
        $em->flush();

        return new JsonResponse(['message' => 'Split marked as paid'], Response::HTTP_OK);
    }
} 