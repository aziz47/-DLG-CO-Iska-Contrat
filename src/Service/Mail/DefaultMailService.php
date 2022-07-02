<?php

namespace App\Service\Mail;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class DefaultMailService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct($customEnv, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(User $user, string $object, string $text)
    {
        $email = (new TemplatedEmail())
            ->from('iska@dlgo-group.ci')
            ->to(new Address(
                $customEnv ?? $user->getEmail()
            ))
            ->subject($object)

            ->htmlTemplate('apps/mails/base.html.twig')

            ->context([
                'title' => $object,
                'text' => $text
            ]);

        $this->mailer->send($email);
    }
}