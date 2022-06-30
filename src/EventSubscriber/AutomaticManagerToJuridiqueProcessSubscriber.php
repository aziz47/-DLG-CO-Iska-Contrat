<?php

namespace App\EventSubscriber;

use App\Entity\Abstracts\ProcessObj;
use App\Service\Mail\DefaultMailService;
use App\Service\ProcessObjToStr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AutomaticManagerToJuridiqueProcessSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager, DefaultMailService $mailService, ProcessObjToStr $processObjToStr)
    {
        $this->manager = $manager;
    }

    public function onWorkflowGestionProcessEntered($event)
    {
        // /* @var ProcessObj $obj */
        // $obj = $event->getSubject();

        // $obj->setCurrentState('demande_acceptee_manager');

        // //Envoi de mail à l'emetteur
        // ($this->mailService)(
        //     $o->getCreatedBy(),
        //     'Demande transmise au service juridique',
        //     'Votre demande ' . ($this->processObjToStr)($o, true) . ' a été tranmise au service juridique.'
        // );

        // $this->manager->persist($obj);
        // $this->manager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.gestion_process.transition.valider_manager' => 'onWorkflowGestionProcessEntered',
        ];
    }
}
