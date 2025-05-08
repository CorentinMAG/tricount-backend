<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\Positive]
    private ?float $amount = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Title cannot be blank")]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'transactions', targetEntity: TransactionLabel::class)]
    #[Assert\NotNull]
    private TransactionLabel $label;

    #[ORM\ManyToOne(inversedBy: 'transactions', targetEntity: TransactionType::class)]
    #[Assert\NotNull]
    private TransactionType $type;

    #[ORM\ManyToOne(inversedBy: 'transactions', targetEntity: User::class)]
    #[Assert\NotNull]
    private User $owner;

    #[ORM\ManyToOne(inversedBy: 'transactions', targetEntity: Tricount::class)]
    #[Assert\NotNull]
    private Tricount $tricount;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'transaction', targetEntity: TransactionSplit::class, cascade: ['persist', 'remove'])]
    private Collection $splits;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->splits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLabel(): TransactionLabel 
    {
        return $this->label;
    }

    public function setLabel(TransactionLabel $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function setType(TransactionType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getTricount(): Tricount
    {
        return $this->tricount;
    }

    public function setTricount(Tricount $tricount): static
    {
        $this->tricount = $tricount;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function canUserAccess(User $user): bool
    {
        return $this->tricount->canUserAccess($user);
    }

    public function canUserEdit(User $user): bool
    {
        return $this->owner === $user || $this->tricount->canUserEdit($user);
    }

    public function getSplits(): Collection
    {
        return $this->splits;
    }

    public function addSplit(TransactionSplit $split): static
    {
        if (!$this->splits->contains($split)) {
            $this->splits->add($split);
            $split->setTransaction($this);
        }
        return $this;
    }

    public function removeSplit(TransactionSplit $split): static
    {
        if ($this->splits->removeElement($split)) {
            if ($split->getTransaction() === $this) {
                $split->setTransaction(null);
            }
        }
        return $this;
    }

    public function validateSplits(): bool
    {
        $totalSplits = 0;
        foreach ($this->splits as $split) {
            $totalSplits += $split->getAmount();
        }
        return abs($totalSplits - $this->amount) < 0.01; // Using small epsilon for float comparison
    }

    public function getTotalPaid(): float
    {
        $total = 0;
        foreach ($this->splits as $split) {
            if ($split->isPaid()) {
                $total += $split->getAmount();
            }
        }
        return $total;
    }

    public function getRemainingAmount(): float
    {
        return $this->amount - $this->getTotalPaid();
    }
}
