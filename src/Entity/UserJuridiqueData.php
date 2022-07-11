<?php

namespace App\Entity;

use App\Repository\UserJuridiqueDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserJuridiqueDataRepository::class)
 */
class UserJuridiqueData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=UserJuridique::class, inversedBy="data", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $UserJuridique;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $nbrJourImpartiContrat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserJuridique(): ?UserJuridique
    {
        return $this->UserJuridique;
    }

    public function setUserJuridique(UserJuridique $UserJuridique): self
    {
        $this->UserJuridique = $UserJuridique;

        return $this;
    }

    public function getNbrJourImpartiContrat(): ?\DateInterval
    {
        return $this->nbrJourImpartiContrat;
    }

    public function setNbrJourImpartiContrat(\DateInterval $nbrJourImpartiContrat): self
    {
        $this->nbrJourImpartiContrat = $nbrJourImpartiContrat;

        return $this;
    }
}
