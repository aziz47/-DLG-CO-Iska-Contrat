<?php

namespace App\Entity\Contrat;

use App\Entity\Abstracts\ProcessObj;
use App\Entity\Departement;
use App\Repository\Contrat\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_contrat")
 * @ORM\Entity(repositoryClass=ContratRepository::class)
 */
class Contrat extends ProcessObj
{
    /**
     * @ORM\ManyToOne(targetEntity=TypeDemandeContrat::class, inversedBy="contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeDemande;

    /**
     * @ORM\Column(type="text")
     */
    private $objet;

    /**
     * @ORM\Column(type="text")
     */
    private $clausesParticulieres;

    /**
     * @ORM\Column(type="date")
     */
    private $entreeVigueurAt;

    /**
     * @ORM\Column(type="date")
     */
    private $finContratAt;

    /**
     * @ORM\ManyToOne(targetEntity=ModeFacturation::class, inversedBy="contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modeFacturation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $delaiDenonciation;

    /**
     * @ORM\Column(type="text")
     */
    private $periodicitePaiement;

    /**
     * @ORM\ManyToOne(targetEntity=ModeReglement::class, inversedBy="contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modeReglement;

    /**
     * @ORM\ManyToOne(targetEntity=ModeRenouvellement::class, inversedBy="contrats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modeRenouvellement;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDerniereEvaluation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identiteCocontractant;

    /**
     * @ORM\Column(type="text")
     */
    private $objetConditionModification;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $libConditionModification;

    /**
     * @ORM\OneToMany(targetEntity=DocumentContrat::class, mappedBy="contrat")
     */
    private $documentContrats;

    public function __construct()
    {
        $this->documentContrats = new ArrayCollection();
    }

    public function getTypeDemande(): ?TypeDemandeContrat
    {
        return $this->typeDemande;
    }

    public function setTypeDemande(?TypeDemandeContrat $typeDemande): self
    {
        $this->typeDemande = $typeDemande;

        return $this;
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

    public function getClausesParticulieres(): ?string
    {
        return $this->clausesParticulieres;
    }

    public function setClausesParticulieres(string $clausesParticulieres): self
    {
        $this->clausesParticulieres = $clausesParticulieres;

        return $this;
    }

    public function getEntreeVigueurAt(): ?\DateTimeInterface
    {
        return $this->entreeVigueurAt;
    }

    public function setEntreeVigueurAt(\DateTimeInterface $entreeVigueurAt): self
    {
        $this->entreeVigueurAt = $entreeVigueurAt;

        return $this;
    }

    public function getFinContratAt(): ?\DateTimeInterface
    {
        return $this->finContratAt;
    }

    public function setFinContratAt(\DateTimeInterface $finContratAt): self
    {
        $this->finContratAt = $finContratAt;

        return $this;
    }

    public function getModeFacturation(): ?ModeFacturation
    {
        return $this->modeFacturation;
    }

    public function setModeFacturation(?ModeFacturation $modeFacturation): self
    {
        $this->modeFacturation = $modeFacturation;

        return $this;
    }

    public function getDelaiDenonciation(): ?string
    {
        return $this->delaiDenonciation;
    }

    public function setDelaiDenonciation(string $delaiDenonciation): self
    {
        $this->delaiDenonciation = $delaiDenonciation;

        return $this;
    }

    public function getPeriodicitePaiement(): ?string
    {
        return $this->periodicitePaiement;
    }

    public function setPeriodicitePaiement(string $periodicitePaiement): self
    {
        $this->periodicitePaiement = $periodicitePaiement;

        return $this;
    }

    public function getModeReglement(): ?ModeReglement
    {
        return $this->modeReglement;
    }

    public function setModeReglement(?ModeReglement $modeReglement): self
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    public function getModeRenouvellement(): ?ModeRenouvellement
    {
        return $this->modeRenouvellement;
    }

    public function setModeRenouvellement(?ModeRenouvellement $modeRenouvellement): self
    {
        $this->modeRenouvellement = $modeRenouvellement;

        return $this;
    }

    public function getDateDerniereEvaluation(): ?\DateTimeInterface
    {
        return $this->dateDerniereEvaluation;
    }

    public function setDateDerniereEvaluation(\DateTimeInterface $dateDerniereEvaluation): self
    {
        $this->dateDerniereEvaluation = $dateDerniereEvaluation;

        return $this;
    }

    public function getIdentiteCocontractant(): ?string
    {
        return $this->identiteCocontractant;
    }

    public function setIdentiteCocontractant(string $identiteCocontractant): self
    {
        $this->identiteCocontractant = $identiteCocontractant;

        return $this;
    }

    public function getObjetConditionModification(): ?string
    {
        return $this->objetConditionModification;
    }

    public function setObjetConditionModification(string $objetConditionModification): self
    {
        $this->objetConditionModification = $objetConditionModification;

        return $this;
    }

    public function getLibConditionModification(): ?string
    {
        return $this->libConditionModification;
    }

    public function setLibConditionModification(?string $libConditionModification): self
    {
        $this->libConditionModification = $libConditionModification;

        return $this;
    }

    /**
     * @return Collection<int, DocumentContrat>
     */
    public function getDocumentContrats(): Collection
    {
        return $this->documentContrats;
    }

    public function addDocumentContrat(DocumentContrat $documentContrat): self
    {
        if (!$this->documentContrats->contains($documentContrat)) {
            $this->documentContrats[] = $documentContrat;
            $documentContrat->setContrat($this);
        }

        return $this;
    }

    public function removeDocumentContrat(DocumentContrat $documentContrat): self
    {
        if ($this->documentContrats->removeElement($documentContrat)) {
            // set the owning side to null (unless already changed)
            if ($documentContrat->getContrat() === $this) {
                $documentContrat->setContrat(null);
            }
        }

        return $this;
    }
}
