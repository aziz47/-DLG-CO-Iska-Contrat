<?php

namespace App\Entity\Autorisation;

use App\Entity\Abstracts\Document;
use App\Repository\Autorisation\DocDemandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocDemandeRepository::class)
 */
class DocDemande extends Document
{

    /**
     * @ORM\ManyToOne(targetEntity=Autorisation::class, inversedBy="docDemandes")
     */
    private $demande;

    public function getDemande(): ?Autorisation
    {
        return $this->demande;
    }

    public function setDemande(?Autorisation $demande): self
    {
        $this->demande = $demande;

        return $this;
    }
}
