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
    private $testMail;

    public function __construct($testMail, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->testMail = $testMail;
    }

    public function __invoke(User $user, string $object, string $text)
    {
        $email = (new TemplatedEmail())
            ->from('iska@dlgo-group.ci')
            ->to(new Address(
                $this->testMail ?? $user->getEmail()
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