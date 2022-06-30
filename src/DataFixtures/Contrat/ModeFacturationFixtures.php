<?php

namespace App\DataFixtures\Contrat;

use App\Entity\Contrat\ModeFacturation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ModeFacturationFixtures extends Fixture
{
    const TYPE_MODE_REFERENCE = 'f-type-mode-facturation-contrat-';
    public function load(ObjectManager $manager): void
    {
        $modeFact = [
            [
                "lib" => "Facturation Mensuelle",
                "color" => "primary"
            ],
            [
                "lib" => "Facturation annuelle",
                "color" => "info"
            ],
            [
                "lib" => "Facturation par phase",
                "color" => "secondary"
            ],
            [
                "lib" => "Facturation par livrable",
                "color" => "secondary"
            ],
        ];

        $i = 0;
        foreach ($modeFact as $val){
            $mFact = (new ModeFacturation())
                ->setLib($val["lib"])
                ->setColor($val["color"]);
            $manager->persist($mFact);
            $manager->flush();
            $this->addReference(self::TYPE_MODE_REFERENCE.$i, $mFact);
            $i++;
        }

        $manager->flush();
    }
}
