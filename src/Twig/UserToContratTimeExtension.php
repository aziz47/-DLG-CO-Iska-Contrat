<?php

namespace App\Twig;

use App\Entity\Contrat\Contrat;
use App\Repository\UserJuridiqueRepository;
use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\User;

class UserToContratTimeExtension extends AbstractExtension
{
    /**
     * @var UserJuridiqueRepository
     */
    private $userJuridiqueRepository;

    public function __construct(UserJuridiqueRepository $userJuridiqueRepository)
    {
        $this->userJuridiqueRepository = $userJuridiqueRepository;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('contrat_time', [$this, 'doSomething']),
        ];
    }

    public function doSomething(User $user, Contrat $contrat)
    {
        if($contrat->isFinished()){
            return [true, 'Traité'];
        }elseif($contrat->getCurrentState() != 'demande_attribuee'){
            return [true, 'En attente d\'attribution'];
        }

        $tempsActuel = Carbon::now();
        $userJ = $this->userJuridiqueRepository->findOneBy([
           'user' => $user
        ]);

        if($userJ){
            $tempsMax = (new Carbon($contrat->getAttributionJuridiqueAt()))
                ->add(
                    $userJ
                        ->getData()
                        ->getNbrJourImpartiContrat()
                );
            $isLimitPassed = $tempsActuel->diff($tempsMax);
            return [
                $isLimitPassed->invert == 0,
                ($isLimitPassed->invert != 0) ? 'Urgent' : ($isLimitPassed->d == 0 ? 'Aujourd\'hui' : $isLimitPassed->d . ' jours restants')
            ];
        }

        return 'Erreur !';
    }
}
