<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Statut;
use App\Repository\Contrat\ModeReglementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_contrat_mode_reglement")
 * @ORM\Entity(repositoryClass=ModeReglementRepository::class)
 */
class ModeReglement extends Statut
{

    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="modeReglement")
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
            $contrat->setModeReglement($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getModeReglement() === $this) {
                $contrat->setModeReglement(null);
            }
        }

        return $this;
    }
}
