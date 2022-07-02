<?php

namespace App\EventSubscriber;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\User;
use App\Repository\UserRepository;
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
    /**
     * @var DefaultMailService
     */
    private $mailService;
    /**
     * @var ProcessObjToStr
     */
    private $processObjToStr;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $manager, DefaultMailService $mailService, ProcessObjToStr $processObjToStr, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->mailService = $mailService;
        $this->processObjToStr = $processObjToStr;
        $this->userRepository = $userRepository;
    }

    public function onWorkflowGestionProcessEntered($event)
    {
        /* @var ProcessObj $obj */
        $obj = $event->getSubject();

        $obj->setCurrentState('demande_acceptee_manager');

        //Envoi de mail à l'emetteur
        ($this->mailService)(
            $obj->getCreatedBy(),
            'Demande transmise au service juridique',
            'Votre demande ' . ($this->processObjToStr)($obj, true) . ' a été tranmise au service juridique.'
        );

        //Envoi de mails aux User Boss
        /* @var User[] $usersJuridiqueBoss */
        $usersJuridiqueBoss = $this->userRepository->findByRoles('ROLE_USER_BOSS_JURIDIQUE');

        foreach ($usersJuridiqueBoss as $userJBoss){
            ($this->mailService)(
                $userJBoss,
                'Nouvelle demande de contrat en attente',
                'Une nouvelle demande de contrat de '. $obj->getCreatedBy()->displayName() .' est en attente.'
            );
        }

         $this->manager->persist($obj);
         $this->manager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.gestion_process.transition.valider_manager' => 'onWorkflowGestionProcessEntered',
        ];
    }
}
