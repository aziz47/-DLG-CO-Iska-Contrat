<?php

namespace App\Service;

/**
 * Service permettant de transformer les current_state codés en chaines compréhensibles
 */
class ProcessObjCurrentStateToStringService
{
    public function __invoke(string $currentState): string
    {
        $vals = [
            "en_attente_manager" => "En attente de validation par le chef de département",
            "demande_rejetee_manager" => "Demande rejétée par le manager",
            "demande_acceptee_manager" => "Demande acceptée par le manager",
            "demande_non_attribuee" => "Demande au niveau du service juridique en attente d'attribution",
            "demande_attribuee" => "Demande attribuée",
            "demande_validee" => "Demande validée",
            "demande_rejetee" => "Demande rejétée",
        ];

        return $vals[$currentState];
    }
}