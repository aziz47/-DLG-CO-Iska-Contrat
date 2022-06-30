<?php

namespace App\Service;

use App\Entity\Abstracts\ProcessObj;
use App\Repository\Abstracts\ProcessObjRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class TraitementJuridiqueProcess
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var WorkflowInterface
     */
    private $gestionProcessStateMachine;
    /**
     * @var ProcessObjRepository
     */
    private $processObjRepository;

    public function __construct(EntityManagerInterface $manager, WorkflowInterface $gestionProcessStateMachine)
    {
        $this->manager = $manager;
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
    }

    /**
     * @throws \Exception
     */
    public function run(ProcessObj $processObj, string $transition){
        try{
            if($transition != 'valider_demande' && $transition != 'refuser_demande'){
                throw new \Exception('La transition est impossible');
            }

            if($this->gestionProcessStateMachine->can($processObj, $transition)){
                $processObj
                    ->setFinalJuridiqueAt(CarbonImmutable::now());
                $this->gestionProcessStateMachine->apply($processObj, $transition);
                $this->manager->persist($processObj);
                $this->manager->flush();
                return true;
            }
        }catch(\Exception $e){
            return false;
        }
    }
}