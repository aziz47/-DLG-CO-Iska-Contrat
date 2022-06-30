<?php

namespace App\DataFixtures\Contrat;

use App\Entity\Contrat\ModeRenouvellement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ModeRenouvellementFixtures extends Fixture
{
    public const MODE_RENOUVELLEMENT_REFERENCE = 'f-mode-renouvellement-';

    public function load(ObjectManager $manager): void
    {
        $options = [
            [
                'lib' => 'Tacite',
                'color' => ''
            ],
            [
                'lib' => 'Express',
                'color' => ''
            ]
        ];

        $i = 0;
        foreach ($options as $o){
            $m = (new ModeRenouvellement())
                ->setLib($o['lib'])
                ->setColor($o['color']);

            $manager->persist($m);
            $manager->flush();
            $this->addReference(self::MODE_RENOUVELLEMENT_REFERENCE.$i, $m);
            $i++;
        }
    }
}
