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

        $user = $event->getUser();

        if ($user instanceof User && $user->getLastLoginIp() != $request->getClientIp()) {
            $user->setLastLoginIp($request->getClientIp());
            $this->em->persist($user);
            $this->em->flush();
        }

        $payload['username'] = $user instanceof User ? $user->getUsername() : null;

        $event->setData($payload);

        $header        = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);
    }
}