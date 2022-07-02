<?php

namespace App\Service;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\Autorisation\Autorisation;
use App\Entity\Autorisation\DocDemande;
use App\Entity\AvisConseils\Avis;
use App\Entity\AvisConseils\DocAvisConseils;
use App\Entity\Contrat\Contrat;
use App\Entity\Contrat\DocumentContrat;
use App\Entity\User;
use App\Repository\Abstracts\ProcessObjRepository;
use App\Repository\UserJuridiqueRepository;
use App\Repository\UserRepository;
use App\Service\Mail\DefaultMailService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class SaveProcessObj
{
    /**
     * @var WorkflowInterface
     */
    private $gestionProcessStateMachine;
    /**
     * @var ProcessObjRepository
     */
    private $processObjRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;
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

    public function __construct(UserRepository $userRepository,DefaultMailService $mailService, ProcessObjToStr $processObjToStr, EntityManagerInterface $manager,WorkflowInterface $gestionProcessStateMachine, ProcessObjRepository $processObjRepository, UserJuridiqueRepository $userJuridiqueRepository)
    {
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
        $this->processObjRepository = $processObjRepository;
        $this->manager = $manager;
        $this->userJuridiqueRepository = $userJuridiqueRepository;
        $this->mailService = $mailService;
        $this->processObjToStr = $processObjToStr;
        $this->userRepository = $userRepository;
    }

    public function run(ProcessObj $processObj, User $user, $options = []){
        $processObj
            ->setCurrentState('en_attente_manager')
            ->setDepartementInitiateur(
                $user->getDepartement()
            )
            ->setCreatedBy($user)
        ;

        if($processObj instanceof Avis){
            if ($this->gestionProcessStateMachine->can($processObj, 'valider_avis')){

                foreach ($options['documents'] as $doc){
                    $fichier = md5(uniqid()).'.'.$doc->guessExtension();
                    $doc->move(
                        $options['upload_folder'],
                        $fichier
                    );
                    $docDB = (new DocAvisConseils())
                        ->setOriginalName($doc->getClientOriginalName())
                        ->setPath($fichier)
                        ->setAvis($processObj);
                    $this->manager->persist($docDB);
                    $processObj->addDocAvisConseil($docDB);
                }
                $this->gestionProcessStateMachine->apply($processObj, 'valider_avis');
                $this->manager->persist($processObj);
                $this->manager->flush();

                //Envoi de mails aux User Boss
                /* @var User[] $usersJuridiqueBoss */
                $usersJuridiqueBoss = $this->userRepository->findByRoles('ROLE_USER_BOSS_JURIDIQUE');

                foreach ($usersJuridiqueBoss as $userJBoss){
                    ($this->mailService)(
                        $userJBoss,
                        'Nouvelle demande d\'avis en attente',
                        'Une nouvelle demande d\'avis de '. $processObj->getCreatedBy()->displayName() .' est en attente.'
                     );
                }
            }
        }
        elseif ($processObj instanceof Contrat){
            foreach ($options['documents'] as $doc){
                $fichier = md5(uniqid()).'.'.$doc->guessExtension();
                $doc->move(
                    $options['upload_folder'],
                    $fichier
                );
                $docDB = (new DocumentContrat())
                    ->setOriginalName($doc->getClientOriginalName())
                    ->setPath($fichier)
                    ->setContrat($processObj);
                $this->manager->persist($docDB);
                $processObj
                    ->addDocumentContrat($docDB);
            }
            $processObj
                ->setDepartementInitiateur(
                    $user->getDepartement()
                )
                ->setDateDerniereEvaluation(Carbon::now());
            //Si l'utilisateur est membre du département juridique, la demande est automatiquement validée.
            if(in_array('ROLE_JURIDIQUE', $user->getRoles())){
                $processObj = $this->saveContratUserJuridique($processObj, $user);
            }
            $this->manager->persist($processObj);
            $this->manager->flush();

            $uManagers = $this->userRepository->findByDepartementManagers($processObj->getCreatedBy()->getDepartement());
            foreach ($uManagers as $uManager) {
                ($this->mailService)(
                   $uManager,
                    'Nouvelle demande de contrat dans le département enregistrée',
                    'Une nouvelle demande de contrat a été émise par ' . $processObj->getCreatedBy()->displayName() . ', elle est en attente de validation de votre part.'
                );
            }
        }
        elseif ($processObj instanceof Autorisation){
            $processObj->setResponse("");
            foreach ($options['documents'] as $doc){
                $fichier = md5(uniqid()).'.'.$doc->guessExtension();
                $doc->move(
                    $options['upload_folder'],
                    $fichier
                );
                $docDB = (new DocDemande())
                    ->setOriginalName($doc->getClientOriginalName())
                    ->setPath($fichier)
                    ->setDemande($processObj);
                $this->manager->persist($docDB);
                $processObj->addDocDemande($docDB);
            }

            //Si l'utilisateur est membre du département juridique, la demande est automatiquement validée.
            if(in_array('ROLE_JURIDIQUE', $user->getRoles())){
                $processObj = $this->saveContratUserJuridique($processObj, $user);
                $processObj
                    ->setDepartementInitiateur(
                        $processObj->getCreatedBy()->getDepartement()
                    );
            }else{
                $processObj
                    ->setDepartementInitiateur(
                        $user->getDepartement()
                    );
            }

            $this->manager->persist($processObj);
            $this->manager->flush();
        }

        ($this->mailService)(
            $processObj->getCreatedBy(),
            'Demande enregistrée',
            'Votre demande ' . ($this->processObjToStr)($processObj, true) . ' a été enregistrée, elle est en cours de traitement.'
        );

        return $processObj;
    }

    /**
     * Fonction personnalisée pour sauver une demande de contrat émanant d'un
     * utilisateur du service juridique.
     */
    private function saveContratUserJuridique(ProcessObj $contrat, User $user) : ProcessObj
    {
        $contrat
            ->setCurrentState('demande_validee')
            ->setUserAgentJuridique(
                $this->userJuridiqueRepository->findOneBy([
                    'user' => $user
                ])
            )
            ->setFinalJuridiqueAt(CarbonImmutable::now())
        ;

        //TODO : Envvoyer un mail personnalisé

        return $contrat;
    }
}