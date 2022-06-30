<?php

namespace App\EventSubscriber;

use App\Entity\Abstracts\ProcessObj;
use App\Service\Mail\DefaultMailService;
use App\Service\ProcessObjToStr;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class AttribuerSubscriber implements EventSubscriberInterface
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

    public function onWorkflowGestionProcessTransition(Event $event)
    {
        /* @var ProcessObj $o */
        $o = $event->getSubject();
        //Envoi de mail à l'emetteur
        ($this->mailService)(
            $o->getUserAgentJuridique()->getUser(),
            'Nouvelle demande',
            'Vous avez une nouvelle demande ' . ($this->processObjToStr)($o, true)  . ' en attente de traitement.'
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.gestion_process.transition.attribuer' => 'onWorkflowGestionProcessTransition',
        ];
    }
}