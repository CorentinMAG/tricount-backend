<?php

namespace App\Entity;

use App\Repository\TransactionSplitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransactionSplitRepository::class)]
class TransactionSplit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['split:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'splits', targetEntity: Transaction::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private Transaction $transaction;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['split:read'])]
    private User $user;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\PositiveOrZero]
    #[Groups(['split:read'])]
    private ?float $amount = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['split:read'])]
    private bool $isPaid = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): static
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
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

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;
        return $this;
    }

    public function markAsPaid(): static
    {
        $this->isPaid = true;
        $this->transaction->setUpdatedAt(new \DateTime());
        return $this;
    }

    public function markAsUnpaid(): static
    {
        $this->isPaid = false;
        $this->transaction->setUpdatedAt(new \DateTime());
        return $this;
    }
} 