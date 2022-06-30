<?php

namespace App\Service;

use App\Entity\Abstracts\ProcessObj;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class ManagerProcess
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var WorkflowInterface
     */
    private $gestionProcessStateMachine;

    public function __construct(EntityManagerInterface $manager, WorkflowInterface $gestionProcessStateMachine)
    {
        $this->manager = $manager;
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
    }

    public function run(ProcessObj $processObj, string $transition)
    {
        try{
            if($transition != 'valider_manager' && $transition != 'refuser_manager'){
                throw new \Exception('L\'action demandée est impossible');
            }

            if($this->gestionProcessStateMachine->can($processObj, $transition)){
                $processObj
                    ->setActionManagerAt(CarbonImmutable::now())
                ;
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