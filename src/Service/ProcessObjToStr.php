<?php

namespace App\Service;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\Autorisation\Autorisation;
use App\Entity\AvisConseils\Avis;
use App\Entity\Contrat\Contrat;

class ProcessObjToStr
{
    public function __invoke(ProcessObj $obj, bool $milieu = false): string
    {
        $str = "";
        $str = ($obj instanceof Avis) ? 'avis' : $str;
        $str = ($obj instanceof  Autorisation) ? 'autorisation' : $str;
        $str = ($obj instanceof  Contrat) ? 'contrat' : $str;

        $str = $milieu ? ( $str == 'contrat' ? 'de '.$str : "d' ".$str ) : $str;

        return $str;
    }
}