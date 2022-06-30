<?php

namespace App\Service\Stats;

use App\Repository\AvisConseils\AvisRepository;
use App\Repository\Stats\UserJuridiqueStatsRepository;
use App\Repository\UserJuridiqueRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class UJuridiquePerfAvisConseils
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserJuridiqueStatsRepository
     */
    private $juridiqueStatsRepository;
    /**
     * @var AvisRepository
     */
    private $avisRepository;
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;

    public function __construct(EntityManagerInterface $manager, UserJuridiqueStatsRepository $juridiqueStatsRepository, AvisRepository $avisRepository, UserJuridiqueRepository $userJuridiqueRepository)
    {
        $this->manager = $manager;
        $this->juridiqueStatsRepository = $juridiqueStatsRepository;
        $this->avisRepository = $avisRepository;
        $this->userJuridiqueRepository = $userJuridiqueRepository;
    }

    public function __invoke()
    {
        print "[STATS SERVICE] Récuperation de tous les agents juridiques " . PHP_EOL;
        //Récupération de tout les membres du service juridique
        $allUJ = $this->userJuridiqueRepository->findAll();
        print "[STATS SERVICE] Liste des agents récupérée " . PHP_EOL;

        foreach ($allUJ as $u){
            print PHP_EOL . '[STATS SERVICE] Traitement de l\'agent' . $u->getUser()->displayName();
            //Récuperation des contrats
            print PHP_EOL . '[STATS SERVICE] Récupération des demandes d\'avis et de conseils';
            $avis = $this->avisRepository->findBy([
                'userAgentJuridique' => $u,
            ]);

            $tempsMis = 0;
            $nbrAvis = 0;
            foreach ($avis as $a){
                print PHP_EOL . '[STATS SERVICE] Calcul pour le contrat ' . $a->getId();
                if($a->getFinalJuridiqueAt() === null){
                    continue;
                }
                $differenceTemps = (new Carbon(
                    $a->getFinalJuridiqueAt()
                ))->diffInSeconds(
                    new Carbon($a->getAttributionJuridiqueAt())
                );

                /* @var Carbon $tempsMis */
                $tempsMis += $differenceTemps;

                $nbrAvis++;
            }

            $uJStats = $this->juridiqueStatsRepository->findOneBy([
                'uJuridique' => $u
            ]);

            $uJStats->setPerfAvisConseils(
                $tempsMis / ( empty($nbrAvis) ? 1 : $nbrAvis )
            );
            $this->manager->persist($uJStats);
            print PHP_EOL . '[STATS SERVICE] Fin de traitement de pour l\'agent' . $u->getUser()->displayName();
        }
        print PHP_EOL . "[STATS SERVICE] FIN DE CALCUL DES STATS " . PHP_EOL;
        $this->manager->flush();
    }
}