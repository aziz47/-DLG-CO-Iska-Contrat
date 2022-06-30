<?php

namespace App\Controller\Process;

use App\Repository\Contrat\ContratRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apps/contrat")
 */
class ContratController extends AbstractController
{
    /**
     * Lister tout les contrats validés
     * @Route("/valides", name="apps_contrat_valides", methods={"GET"})
     */
    public function valides(ContratRepository $contratRepository){
        $contrats = $contratRepository->findBy([
            'currentState' => 'demande_validee'
        ]);

        $echeanceCount = 0;
        foreach ($contrats as $contrat){
            //Calcul du delai de dénonciation
            $echeance = ((Carbon::now())->diff($contrat->getFinContratAt()));

            //La date est déjà passée
            if($echeance->invert === 1){
                $echeanceCount++;
            }else{
                $nombreDeMoisAvantDenonciation = intval(str_split($contrat->getDelaiDenonciation())[0]);
                //On calcule le temps restant
                $mois = intval(($echeance->m) + (12 * ($echeance->y)) + floor(($echeance->d) / 31));

                $echeanceCount += $nombreDeMoisAvantDenonciation > $mois ? 1 : 0;
            }
        }

        return $this->render('apps/gestion_process/contrat_pages/valide.html.twig', [
           'echeance' => $echeanceCount,
           'contrats' => $contrats,
           'total' => count($contrats)
        ]);
    }
}