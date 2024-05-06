<?php

namespace App\Entity;

use App\Repository\TricountLabelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TricountLabelRepository::class)]
class TricountLabel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'label', targetEntity: Tricount::class)]
    private Collection $tricounts;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->tricounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt() : \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getTricounts(): Collection
    {
        return $this->tricounts;
    }
}
