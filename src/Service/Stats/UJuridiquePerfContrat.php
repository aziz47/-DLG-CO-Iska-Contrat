<?php

namespace App\Service\Stats;

use App\Repository\Contrat\ContratRepository;
use App\Repository\Stats\UserJuridiqueStatsRepository;
use App\Repository\UserJuridiqueRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;

class UJuridiquePerfContrat
{
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;
    /**
     * @var ContratRepository
     */
    private $contratRepository;
    /**
     * @var UserJuridiqueStatsRepository
     */
    private $juridiqueStatsRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager, UserJuridiqueStatsRepository $juridiqueStatsRepository, UserJuridiqueRepository $userJuridiqueRepository, ContratRepository $contratRepository)
    {
        $this->userJuridiqueRepository = $userJuridiqueRepository;
        $this->contratRepository = $contratRepository;
        $this->juridiqueStatsRepository = $juridiqueStatsRepository;
        $this->manager = $manager;
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
            print PHP_EOL . '[STATS SERVICE] Récupération des contrats';
            $contrats = $this->contratRepository->findBy([
                'userAgentJuridique' => $u
            ]);

            $tempsMis = 0;
            $nbrContrats = 0;
            foreach ($contrats as $contrat){
                print PHP_EOL . '[STATS SERVICE] Calcul pour le contrat ' . $contrat->getId();
                if($contrat->getFinalJuridiqueAt() === null){
                    continue;
                }
                $differenceTemps = (new Carbon(
                    $contrat->getFinalJuridiqueAt()
                ))->diffInSeconds(
                  new Carbon($contrat->getAttributionJuridiqueAt())
                );

                /* @var Carbon $tempsMis */
                $tempsMis += $differenceTemps;

                $nbrContrats++;
            }

            $uJStats = $this->juridiqueStatsRepository->findOneBy([
                'uJuridique' => $u
            ]);

            $uJStats->setPerfContrat(
                $tempsMis /  ( empty($nbrContrats) ? 1 : $nbrContrats )
            );
            $this->manager->persist($uJStats);
            print PHP_EOL . '[STATS SERVICE] Fin de traitement de pour l\'agent' . $u->getUser()->displayName();
        }
        print PHP_EOL . "[STATS SERVICE] FIN DE CALCUL DES STATS " . PHP_EOL;
        $this->manager->flush();
    }
}