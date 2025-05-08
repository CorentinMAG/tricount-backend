<?php

namespace App\Entity;

use App\Repository\TricountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: TricountRepository::class)]
class Tricount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tricount:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['tricount:read'])]
    private ?string $title = null;

    #[ORM\Column(nullable: true, type: 'text')]
    #[Assert\Length(max: 1000)]
    #[Groups(['tricount:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['tricount:read'])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tricounts', targetEntity: TricountLabel::class)]
    #[Assert\NotNull]
    #[Groups(['tricount:read'])]
    private TricountLabel $label;

    #[ORM\ManyToOne(inversedBy: 'tricounts', targetEntity: Currency::class)]
    #[Assert\NotNull]
    #[Groups(['tricount:read'])]
    private Currency $currency;

    #[Vich\UploadableField(mapping: 'tricounts', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Please upload a valid image file (JPEG or PNG)'
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['tricount:read'])]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tricount:read'])]
    private ?int $imageSize = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'tricount_users')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'tricounts', targetEntity: User::class)]
    #[Assert\NotNull]
    private User $owner;

    #[ORM\OneToMany(targetEntity:Transaction::class, mappedBy: 'tricount', cascade: ['remove'])]
    private Collection $transactions;

    #[ORM\Column(nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['tricount:read'])]
    private bool $isActive = true;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->transactions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->token = bin2hex(random_bytes(10));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLabel(): TricountLabel
    {
        return $this->label;
    }

    public function setLabel(TricountLabel $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;
        return $this;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
        }
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
        return $this;
    }

    public function getOwner(): User {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;
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
        return $this->owner === $user || $this->users->contains($user);
    }

    public function canUserEdit(User $user): bool
    {
        return $this->owner === $user;
    }

    public function getTotalBalance(): float
    {
        $total = 0;
        foreach ($this->transactions as $transaction) {
            $total += $transaction->getAmount();
        }
        return $total;
    }

    public function getBalanceForUser(User $user): float
    {
        $balance = 0;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getOwner() === $user) {
                $balance += $transaction->getAmount();
            }
            foreach ($transaction->getSplits() as $split) {
                if ($split->getUser() === $user) {
                    $balance -= $split->getAmount();
                }
            }
        }
        return $balance;
    }

    public function getUnpaidAmountForUser(User $user): float
    {
        $total = 0;
        foreach ($this->transactions as $transaction) {
            foreach ($transaction->getSplits() as $split) {
                if ($split->getUser() === $user && !$split->isPaid()) {
                    $total += $split->getAmount();
                }
            }
        }
        return $total;
    }

    public function getPaidAmountForUser(User $user): float
    {
        $total = 0;
        foreach ($this->transactions as $transaction) {
            foreach ($transaction->getSplits() as $split) {
                if ($split->getUser() === $user && $split->isPaid()) {
                    $total += $split->getAmount();
                }
            }
        }
        return $total;
    }

    public function getActiveTransactions(): Collection
    {
        return $this->transactions->filter(function(Transaction $transaction) {
            return $transaction->isActive();
        });
    }

    public function getInactiveTransactions(): Collection
    {
        return $this->transactions->filter(function(Transaction $transaction) {
            return !$transaction->isActive();
        });
    }

    public function getActiveUsers(): Collection
    {
        return $this->users->filter(function(User $user) {
            return $user->getLastLoginAt() !== null;
        });
    }

    public function generateNewToken(): string
    {
        $this->token = bin2hex(random_bytes(10));
        return $this->token;
    }

    public function getTransactionsByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection
    {
        return $this->transactions->filter(function(Transaction $transaction) use ($startDate, $endDate) {
            return $transaction->getCreatedAt() >= $startDate && $transaction->getCreatedAt() <= $endDate;
        });
    }

    #[Groups(['tricount:read'])]
    #[SerializedName('members')]
    public function getMembersNumber(): int
    {
        return count($this->getUsers()) + 1;
    }
}
