<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class SignupController extends AbstractController
{
    #[Route('/api/signup', name: 'signup', methods: ['POST'])]
    public function signup(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
        ): JsonResponse
    {

        $content = $request->toArray();

        $user = new User();
        $user->setEmail($content['email']);
        $user->setUsername($content['username']);

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $plaintTextPassword = $content['password'];
        $hashedPassword = $passwordHasher->hashPassword($user, $plaintTextPassword);
        $user->setPassword($hashedPassword);
        $user->setLastLoginIp($request->getClientIp());
        $em->persist($user);
        $em->flush();

        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

}
