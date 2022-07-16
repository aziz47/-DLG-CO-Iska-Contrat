<?php

namespace App\Service\Mail;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class DefaultMailService
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function __invoke(User $user, string $object, string $text)
    {
        try {
            $this->logger->info(
                "[SERVICE ENVOI DE MAIL] " .
                "To : " . $user->getEmail() . " | " .
                "Object : " . $object
            );
            $email = (new TemplatedEmail())
                ->from('iska@dlgo-group.ci')
                ->to(new Address(
                    $user->getEmail()
                ))
                ->subject($object)

                ->htmlTemplate('apps/mails/base.html.twig')

                ->context([
                    'title' => $object,
                    'text' => $text
                ]);

            $this->mailer->send($email);
            $this->logger->info(
                "[SERVICE ENVOI DE MAIL] Envoi réussi !"
            );
        }catch (\Exception $e){
            $this->logger->error(
                "[SERVICE ENVOI DE MAIL] Erreur ! - " . $e->getMessage()
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(
                "[SERVICE ENVOI DE MAIL] Erreur ! - " . $e->getMessage()
            );
        }

    }
}