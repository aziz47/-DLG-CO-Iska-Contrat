<?php

namespace App\DataFixtures\Contrat;

use App\Entity\Contrat\ModeReglement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ModeReglementFixtures extends Fixture
{
    const TYPE_MODE_REFERENCE = 'f-type-mode-reglement-contrat-';
    public function load(ObjectManager $manager): void
    {
        $modeRegl = [
            [
                "lib" => "Mode de paiement par chèque",
                "color" => "primary"
            ],
            [
                "lib" => "Mode de paiement par virement",
                "color" => "info"
            ],
        ];

        $i = 0;
        foreach ($modeRegl as $m){
            $modRgl = (new ModeReglement())
                ->setLib($m['lib'])
                ->setColor($m['color']);

            $manager->persist($modRgl);
            $manager->flush();
            $this->addReference(self::TYPE_MODE_REFERENCE.$i, $modRgl);
            $i++;
        }
    }
}
