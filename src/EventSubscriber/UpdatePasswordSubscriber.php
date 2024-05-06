<?php

namespace App\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdatePasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em, 
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UpdatePasswordEvent::class => 'onUpdatePassword',
        ];
    }

    public function onUpdatePassword(UpdatePasswordEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $plainPassword = $event->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        $this->em->flush();
    }
}