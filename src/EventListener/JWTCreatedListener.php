<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class JWTCreatedListener {
    /**
     * @var RequestStack
     */
    private $requestStack;

    private $em;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload       = $event->getData();
        $payload['ip'] = $request->getClientIp();

        $user = $event->getUser(); # get the user object

        if (!$user instanceof User) {
            return;
        }

        if ($user->getLastLoginIp() != $request->getClientIp()) {
            $user->setLastLoginIp($request->getClientIp());
            $user->setLastLoginAt(new \DateTime());
            $this->em->persist($user);
            $this->em->flush();
        }

        $event->setData($payload);

        $header        = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}