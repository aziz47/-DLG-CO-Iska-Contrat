<?php

namespace App\Entity\Abstracts;

use App\Repository\Abstracts\StatutRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Table(name="t_statut")
 * @Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\Entity(repositoryClass=StatutRepository::class)
 * @DiscriminatorMap({
 *     "statut" = "Statut",
 *     "type_demande_contrat" = "App\Entity\Contrat\TypeDemandeContrat",
 *     "mode_facturation_contrat" = "App\Entity\Contrat\ModeFacturation",
 *     "mode_reglement_contrat" = "App\Entity\Contrat\ModeReglement",
 *     "mode_renouvellement_contrat" = "App\Entity\Contrat\ModeRenouvellement",
 *     "identite_cocontractant" = "App\Entity\Contrat\IdentiteCocontractant"
 * })
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Statut
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lib;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLib(): ?string
    {
        return $this->lib;
    }

    public function setLib(string $lib): self
    {
        $this->lib = $lib;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = CarbonImmutable::now();
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return $this
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = Carbon::now();
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
