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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/api/tricounts')]
class TricountController extends AbstractController
{
    #[Route('', name: 'create_tricount', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        
        $tricount = new Tricount();

        $file = $request->files->get('image');

        if (isset($file)) {
            $tricount->setImageFile($file);
        } else {
            $tricount->setImageFile(new UploadedFile($this->getParameter('kernel.project_dir') . '/default/money-bag.png', 'money-bag.png', null, null, true));
        }

        $jsondata = $request->request->get('data');
        $data = json_decode($jsondata, true);
        
        $tricount->setTitle($data['title']);

        if(isset($data['description'])) {
            $tricount->setDescription($data['description']);
        }
        $tricount->setOwner($user);
        
        $currency = $em->getRepository(Currency::class)->find($data['currency']);
        if ($currency) {
            $tricount->setCurrency($currency);
        }
        
        $label = $em->getRepository(TricountLabel::class)->find($data['label']);
        if ($label) {
            $tricount->setLabel($label);
        }

        $errors = $validator->validate($tricount);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($tricount);
        $em->flush();

        $jsonTricount = $serializer->serialize($tricount, 'json', ['groups' => ['tricount:read', 'currency:read', 'tricountlabel:read']]);
        return new JsonResponse($jsonTricount, Response::HTTP_CREATED, [], true);
    }

    #[Route('', name: 'list_tricounts', methods: ['GET'])]
    public function list(TricountRepository $repo, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $tricounts = $repo->getByUser($user);
        $jsonTricounts = $serializer->serialize($tricounts, 'json', ['groups' => ['tricount:read', 'currency:read', 'tricountlabel:read']]);
        return new JsonResponse($jsonTricounts, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'get_tricount', methods: ['GET'])]
    public function get(Tricount $tricount, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $jsonTricount = $serializer->serialize($tricount, 'json', ['groups' => ['tricount:read']]);
        return new JsonResponse($jsonTricount, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'update_tricount', methods: ['PUT'])]
    public function update(
        Tricount $tricount, 
        Request $request, 
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserEdit($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $tricount->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $tricount->setDescription($data['description']);
        }
        if (isset($data['currency'])) {
            $currency = $em->getRepository(Currency::class)->find($data['currency']);
            if ($currency) {
                $tricount->setCurrency($currency);
            }
        }
        if (isset($data['label'])) {
            $label = $em->getRepository(TricountLabel::class)->find($data['label']);
            if ($label) {
                $tricount->setLabel($label);
            }
        }

        $errors = $validator->validate($tricount);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        $jsonTricount = $serializer->serialize($tricount, 'json', ['groups' => ['tricount:read']]);
        return new JsonResponse($jsonTricount, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name: 'delete_tricount', methods: ['DELETE'])]
    public function delete(Tricount $tricount, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserEdit($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $em->remove($tricount);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/join/{token}', name: 'join_tricount', methods: ['POST'])]
    public function join(string $token, TricountRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $tricount = $repo->getByToken($token);
        if (!$tricount || !$tricount->isActive()) {
            return new JsonResponse(['message' => 'Invalid or inactive tricount'], Response::HTTP_NOT_FOUND);
        }

        if ($tricount->getOwner() === $user) {
            return new JsonResponse(['message' => 'You are already the owner of this tricount'], Response::HTTP_BAD_REQUEST);
        }

        if ($tricount->getUsers()->contains($user)) {
            return new JsonResponse(['message' => 'You are already a member of this tricount'], Response::HTTP_BAD_REQUEST);
        }

        $tricount->addUser($user);
        $em->persist($tricount);
        $em->flush();

        return new JsonResponse(['message' => 'Successfully joined the tricount'], Response::HTTP_OK);
    }

    #[Route('/{id}/statistics', name: 'tricount_statistics', methods: ['GET'])]
    public function statistics(Tricount $tricount, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserAccess($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $statistics = [
            'total_balance' => $tricount->getTotalBalance(),
            'users' => []
        ];

        foreach ($tricount->getUsers() as $member) {
            $statistics['users'][] = [
                'id' => $member->getId(),
                'username' => $member->getUsername(),
                'balance' => $tricount->getBalanceForUser($member),
                'unpaid_amount' => $tricount->getUnpaidAmountForUser($member),
                'paid_amount' => $tricount->getPaidAmountForUser($member)
            ];
        }

        // Add owner statistics
        $statistics['users'][] = [
            'id' => $tricount->getOwner()->getId(),
            'username' => $tricount->getOwner()->getUsername(),
            'balance' => $tricount->getBalanceForUser($tricount->getOwner()),
            'unpaid_amount' => $tricount->getUnpaidAmountForUser($tricount->getOwner()),
            'paid_amount' => $tricount->getPaidAmountForUser($tricount->getOwner())
        ];

        return new JsonResponse($statistics, Response::HTTP_OK);
    }

    #[Route('/{id}/regenerate-token', name: 'regenerate_tricount_token', methods: ['POST'])]
    public function regenerateToken(Tricount $tricount, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$tricount->canUserEdit($user)) {
            return new JsonResponse(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $newToken = $tricount->generateNewToken();
        $tricount->setJoinUri($this->generateUrl('join_tricount', ['token' => $newToken], UrlGeneratorInterface::ABSOLUTE_URL));
        
        $em->flush();

        return new JsonResponse([
            'token' => $newToken,
            'join_uri' => $tricount->getJoinUri()
        ], Response::HTTP_OK);
    }
}