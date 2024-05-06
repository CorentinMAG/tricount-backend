<?php

namespace App\EventSubscriber;

// ...
use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

final class MailerSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Symfony 4.3 and inferior, use 'coop_tilleuls_forgot_password.create_token' event name
            CreateTokenEvent::class => 'onCreateToken',
        ];
    }

    public function onCreateToken(CreateTokenEvent $event): void
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $email = (new TemplatedEmail())
            ->from('app.noreply.acc@gmail.com')
            ->to($user->getEmail())
            ->subject('Reset your password')
            ->htmlTemplate('email/password_reset.html.twig')
            ->context([
                'url' => "http://localhost:8000/forgot-password/{$passwordToken->getToken()}"
            ]);
        $this->mailer->send($email);
    }
}