<?php

namespace App\EventSubscriber;

use App\Entity\Abstracts\ProcessObj;
use App\Service\Mail\DefaultMailService;
use App\Service\ProcessObjToStr;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Event\Event;

class ValiderAvisSubscriber implements EventSubscriberInterface
{
    /**
     * @var DefaultMailService
     */
    private $mailService;

    public function __construct(DefaultMailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function onWorkflowGestionProcessTransition(Event $event)
    {
        //@var ProcessObj $o
        /*
        $o = $event->getSubject();

        ($this->mailService)(
            $o->getCreatedBy(),
            'Demande transmise',
            'Votre demande d\'avis a été transmise au service juridique.'
        ); */
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.gestion_process.transition.valider_avis' => 'onWorkflowGestionProcessTransition',
        ];
    }
}
