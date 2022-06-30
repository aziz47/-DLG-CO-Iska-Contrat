<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ProcessObjFiltersExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('processObjFilter', [$this, 'getValue']),
        ];
    }

    public function getValue($value): string
    {
        $vals = [
    		'demande_non_attribuee' => 'En attente - Service Juridique',
		'avis_title' => 'Avis & consultations',
            'avis_new_title' => "Nouvelle demande d'avis & de consultations",
            'avis_modif_title' => "Modifier la demande d'avis & de consultations",
            'avis_new_block_title' => "Effectuer une demande d'avis / consultation",

            'contrat_title' => 'Gestion Contractuelle',
            'contrat_new_title' => "Nouvelle demande de contrat",
            'contrat_modif_title' => "Modifier la demande de contrat",
            'contrat_new_block_title' => "Effectuer une nouvelle demande de contrat",

            'autorisation_title' => 'Gestion Autorisations',
            'autorisation_new_title' => "Nouvelle demande d'autorisation",
            'autorisation_modif_title' => "Modifier la demande d'autorisation",
            'autorisation_new_block_title' => "Effectuer une nouvelle demande d'autorisation"
        ];

        return $vals[$value];
    }
}
