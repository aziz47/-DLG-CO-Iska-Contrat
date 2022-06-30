<?php

namespace App\Entity\Stats;

use App\Entity\UserJuridique;
use App\Repository\Stats\UserJuridiqueStatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserJuridiqueStatsRepository::class)
 */
class UserJuridiqueStats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=UserJuridique::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $uJuridique;

    /**
     * Temps en secondes de traitement des contrats
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfContrat;

    /**
     * Temps en secondes de traitement des demandes d'avis et conseils
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perfAvisConseils;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUJuridique(): ?UserJuridique
    {
        return $this->uJuridique;
    }

    public function setUJuridique(UserJuridique $uJuridique): self
    {
        $this->uJuridique = $uJuridique;

        return $this;
    }

    public function getPerfContrat(): ?int
    {
        return $this->perfContrat;
    }

    public function setPerfContrat(?int $perfContrat): self
    {
        $this->perfContrat = $perfContrat;

        return $this;
    }

    public function getPerfAvisConseils(): ?int
    {
        return $this->perfAvisConseils;
    }

    public function setPerfAvisConseils(?int $perfAvisConseils): self
    {
        $this->perfAvisConseils = $perfAvisConseils;

        return $this;
    }
}
