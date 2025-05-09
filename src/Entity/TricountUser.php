<?php

namespace App\Entity;

use App\Repository\TricountUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TricountUserRepository::class)]
class TricountUser
{
    public const ROLE_OWNER = 'owner';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_VIEWER = 'viewer';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tricountUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private Tricount $tricount;

    #[ORM\ManyToOne(inversedBy: 'tricountUsers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tricount:read'])]
    private User $user;

    #[ORM\Column(length: 20)]
    #[Groups(['tricount:read'])]
    private string $role = self::ROLE_VIEWER;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        if (!in_array($role, [self::ROLE_OWNER, self::ROLE_EDITOR, self::ROLE_VIEWER])) {
            throw new \InvalidArgumentException('Invalid role');
        }
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function canEdit(): bool
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_EDITOR]);
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }
} 