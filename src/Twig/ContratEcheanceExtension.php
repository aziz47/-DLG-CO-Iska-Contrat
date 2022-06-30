<?php

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContratEcheanceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('contratEcheance', [$this, 'getFinAt']),
        ];
    }

    public function getFinAt(\DateTime $dateFinContrat)
    {
        $echeance = ((Carbon::now())->diff($dateFinContrat));

        $delai = "";
        if($echeance->invert === 1){
            $delai .= 'Contrat échu !';
        }else{
            $delai = $echeance->y > 0 ? $echeance->y.' années ' : '';
            $delai .= $echeance->m > 0 ? $echeance->m.' mois ' : '';
            $delai .= $echeance->d > 0 ? $echeance->d.' jours.' : '';
        }

        return $delai;
    }
}
