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
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class EditProcessObj
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

    public function __construct(EntityManagerInterface $manager,WorkflowInterface $gestionProcessStateMachine, ProcessObjRepository $processObjRepository, UserJuridiqueRepository $userJuridiqueRepository)
    {
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
        $this->processObjRepository = $processObjRepository;
        $this->manager = $manager;
        $this->userJuridiqueRepository = $userJuridiqueRepository;
    }

    public function run(ProcessObj $processObj, ProcessObj $oldProcessObj, $options = []){
        $processObj->setDepartementInitiateur(
            $options['departement']
        );

        if($processObj instanceof Avis && $oldProcessObj instanceof Avis){
            foreach ($oldProcessObj->getDocAvisConseils() as $doc){
                $processObj->addDocAvisConseil($doc);
            }

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
            $this->manager->persist($processObj);
            $this->manager->flush();
        }
        elseif ($processObj instanceof Contrat && $oldProcessObj instanceof Contrat){
            foreach ($oldProcessObj->getDocumentContrats() as $doc){
                $processObj->addDocumentContrat($doc);
            }

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
            $this->manager->persist($processObj);
            $this->manager->flush();
        }
        elseif ($processObj instanceof Autorisation && $oldProcessObj instanceof Autorisation){
            foreach ($oldProcessObj->getDocDemandes() as $doc){
                $processObj->addDocDemande($doc);
            }

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
            $this->manager->persist($processObj);
            $this->manager->flush();
        }

        return $processObj;
    }
}