<?php

namespace App\Service;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\User;
use App\Entity\UserJuridique;
use App\Repository\Abstracts\ProcessObjRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\WorkflowInterface;

class AttributionJuridiqueProcess
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
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(EntityManagerInterface $manager,WorkflowInterface $gestionProcessStateMachine, ProcessObjRepository $processObjRepository, MailerInterface $mailer)
    {// ...
        $this->gestionProcessStateMachine = $gestionProcessStateMachine;
        $this->processObjRepository = $processObjRepository;
        $this->manager = $manager;
        $this->mailer = $mailer;
    }

    public function run(ProcessObj $processObj, UserJuridique $user){
        //try {
            if(
                $this->gestionProcessStateMachine->can($processObj, 'attribuer')
            ){
                $processObj
                    ->setUserAgentJuridique($user)
                    ->setAttributionJuridiqueAt(
                        CarbonImmutable::now()
                    );

                $this->gestionProcessStateMachine->apply($processObj, 'attribuer');

                $this->manager->persist($processObj);
                $this->manager->flush();


                return true;
            }
        /*}catch (\Exception $e){ }
        return false;*/
    }
}