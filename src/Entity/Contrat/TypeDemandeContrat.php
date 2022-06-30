<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Statut;
use App\Repository\Contrat\TypeDemandeContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_contrat_type_demande_contrat")
 * @ORM\Entity(repositoryClass=TypeDemandeContratRepository::class)
 */
class TypeDemandeContrat extends Statut
{
    /**
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="typeDemande")
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
            $contrat->setTypeDemande($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): self
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getTypeDemande() === $this) {
                $contrat->setTypeDemande(null);
            }
        }

        return $this;
    }
}
