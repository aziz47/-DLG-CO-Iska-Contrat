<?php

namespace App\DataFixtures\Contrat;

use App\Entity\Contrat\TypeDemandeContrat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeDemandeFixtures extends Fixture
{
    public const TYPE_DEMANDE_REFERENCE = 'f-type-demande-contrat-';
    public function load(ObjectManager $manager): void
    {
        $typeDemandeTab = [
            [
                "lib" => "Construction",
                "color" => "primary"
            ],
            [
                "lib" => "Entretien / Maintenance",
                "color" => "info"
            ],
            [
                "lib" => "Prestation de services",
                "color" => "secondary"
            ],
            [
                "lib" => "Transport de fonds",
                "color" => "info"
            ],
            [
                "lib" => "Vente",
                "color" => "info"
            ],
            [
                "lib" => "Entretien / Nettoyage",
                "color" => "info"
            ],
            [
                "lib" => "Demande d'autorisation",
                "color" => "info"
            ],
            [
                "lib" => "Bail",
                "color" => "info"
            ],
            [
                "lib" => "Construction",
                "color" => "info"
            ],
            [
                "lib" => "Accord de confidentialité",
                "color" => "info"
            ],
        ];

        $i = 0;
        foreach ($typeDemandeTab as $val){
            $typeDemande = (new TypeDemandeContrat())
                ->setLib($val["lib"])
                ->setColor($val["color"]);
            $manager->persist($typeDemande);
            $manager->flush();
            $this->addReference(self::TYPE_DEMANDE_REFERENCE.$i, $typeDemande);
            $i++;
        }

        $manager->flush();
    }
}
