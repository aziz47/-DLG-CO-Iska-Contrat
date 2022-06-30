<?php

namespace App\Entity\Autorisation;

use App\Entity\Abstracts\ProcessObj;
use App\Repository\Autorisation\AutorisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name = "t_demande_autorisation")
 * @ORM\Entity(repositoryClass=AutorisationRepository::class)
 */
class Autorisation extends ProcessObj
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $objet;

    /**
     * @ORM\Column(type="text")
     */
    private $corps;

    /**
     * @ORM\Column(type="text")
     */
    private $response;

    /**
     * @ORM\OneToMany(targetEntity=DocDemande::class, mappedBy="demande")
     */
    private $docDemandes;

    public function __construct()
    {
        $this->docDemandes = new ArrayCollection();
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return Collection<int, DocDemande>
     */
    public function getDocDemandes(): Collection
    {
        return $this->docDemandes;
    }

    public function addDocDemande(DocDemande $docDemande): self
    {
        if (!$this->docDemandes->contains($docDemande)) {
            $this->docDemandes[] = $docDemande;
            $docDemande->setDemande($this);
        }

        return $this;
    }

    public function removeDocDemande(DocDemande $docDemande): self
    {
        if ($this->docDemandes->removeElement($docDemande)) {
            // set the owning side to null (unless already changed)
            if ($docDemande->getDemande() === $this) {
                $docDemande->setDemande(null);
            }
        }

        return $this;
    }
}
