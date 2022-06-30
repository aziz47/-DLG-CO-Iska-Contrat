<?php

namespace App\Entity\Abstracts;

use App\Repository\AbstractsRepo\DocumentRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table (name="t_document")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({
 *     "mod_contrat" = "App\Entity\BaseDoc\ModeleContrat",
 *     "obligation" = "App\Entity\Obligation\Document",
 *     "avis_conseils" = "App\Entity\AvisConseils\DocAvisConseils",
 *     "doc_contrat" = "App\Entity\Contrat\DocumentContrat",
 *     "doc_demande_autorisaton" = "App\Entity\Autorisation\DocDemande",
 * })
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
abstract class Document
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
    private $path;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalName;

    /**
     * @Assert\Unique()
     * @ORM\Column(type="string", length=500)
     */
    private $slug;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     * @return $this
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
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     * @return $this
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = Carbon::now();

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist
     */
    public function setSlug(): self
    {
        $faker = \Faker\Factory::create('fr_FR');
        $this->slug = $faker->uuid;

        return $this;
    }
}
