<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $dep = (new Departement())
            ->setLib("Administrateur")
            ->setColor("yellow");

        $manager->persist($dep);

        $user = (new User())
            ->setFirstName("Admin")
            ->setLastName("Admin")
            ->setDepartement($dep)
            ->setEmail("admin@local.lan")
            ->setRoles(["ROLE_ADMIN"]);

        $user->setPassword(
            $this->hasher->hashPassword(
                $user, 'azerty'
            )
        );
        $manager->persist($user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['admin-fixtures'];
    }
}
