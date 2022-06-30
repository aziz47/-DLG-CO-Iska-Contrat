<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Statut;
use App\Repository\Contrat\ModeRenouvellementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModeRenouvellementRepository::class)
 */
class ModeRenouvellement extends Statut
{
    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="modeRenouvellement")
     */
    private $contrats;

    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): self
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats[] = $contrat;
            $contrat->setModeRenouvellement($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getModeRenouvellement() === $this) {
                $contrat->setModeRenouvellement(null);
            }
        }

        return $this;
    }
}
