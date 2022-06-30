<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\Document;
use App\Repository\Contrat\DocumentContratRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentContratRepository::class)
 */
class DocumentContrat extends Document
{
    /**
     * @ORM\ManyToOne(targetEntity=Contrat::class, inversedBy="documentContrats")
     */
    private $contrat;

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): self
    {
        $this->contrat = $contrat;

        return $this;
    }
}
