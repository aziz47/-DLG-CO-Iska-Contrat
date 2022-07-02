<?php

namespace App\EventSubscriber;

use App\Entity\Abstracts\ProcessObj;
use App\Service\Mail\DefaultMailService;
use App\Service\ProcessObjToStr;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RefuserJuridiqueSubscriber implements EventSubscriberInterface
{

    /**
     * @var DefaultMailService
     */
    private $mailService;
    /**
     * @var ProcessObjToStr
     */
    private $processObjToStr;

    public function __construct(DefaultMailService $mailService, ProcessObjToStr $processObjToStr)
    {
        $this->mailService = $mailService;
        $this->processObjToStr = $processObjToStr;
    }

    public function onWorkflowGestionProcessTransitionRefuserDemande($event)
    {
        /* @var ProcessObj $o */
        $o = $event->getSubject();
        //Envoi de mail à l'emetteur

        ($this->mailService)(
            $o->getCreatedBy(),
            'Demande refusée',
            'Votre demande ' . ($this->processObjToStr)($o, true) . ' a été refusée.'
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.gestion_process.transition.refuser_demande' => 'onWorkflowGestionProcessTransitionRefuserDemande',
        ];
    }
}
