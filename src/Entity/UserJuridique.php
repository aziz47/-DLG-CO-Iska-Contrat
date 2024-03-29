<?php

namespace App\Entity;

use App\Entity\GestionContentieux\Litige;
use App\Repository\UserJuridiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="t_user_juridiqiue")
 * @ORM\Entity(repositoryClass=UserJuridiqueRepository::class)
 */
class UserJuridique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=UserJuridiqueData::class, mappedBy="UserJuridique", cascade={"persist", "remove"})
     */
    private $data;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getData(): ?UserJuridiqueData
    {
        return $this->data;
    }

    public function setData(UserJuridiqueData $data): self
    {
        // set the owning side of the relation if necessary
        if ($data->getUserJuridique() !== $this) {
            $data->setUserJuridique($this);
        }

        $this->data = $data;

        return $this;
    }

}
