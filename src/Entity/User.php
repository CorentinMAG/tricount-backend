<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: "This email address is already in use")]
#[UniqueEntity(fields: ['username'], message: "This username is already in use")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 100)]
    #[Assert\Email]
    #[Groups(["user:read"])]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 40)]
    #[Groups(["user:read"])]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[Vich\UploadableField(mapping: 'avatars', fileNameProperty: 'avatarName', size: 'avatarSize')]
    private ?File $avatarFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $avatarSize = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["user:read"])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Groups(["user:read"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(length: 2, nullable: false, options: ['default' => 'FR'])]
    #[Groups(["user:read"])]
    private string $country = 'FR';

    #[ORM\Column(type: 'string', options: ['default' => null], nullable: true)]
    #[Groups(["user:read"])]
    private ?string $lastLoginIp = null;

    #[ORM\Column(type: 'datetime', options: ['default' => null], nullable: true)]
    #[Groups(["user:read"])]
    private ?\DateTimeInterface $lastLoginAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TricountUser::class)]
    private Collection $tricountUsers;

    #[Assert\Url]
    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(["user:read"])]
    private ?string $gravatar;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->transactions = new ArrayCollection();
        $this->tricountUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        $hash = hash('sha256', $email);

        $this->gravatar = 'https://www.gravatar.com/avatar/'.$hash.'.jpg?s=200&d=identicon';

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(string $lastLoginIp): static
    {
        $this->lastLoginIp = $lastLoginIp;
        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(\DateTimeInterface $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): static
    {
        $this->avatarName = $avatarName;
        return $this;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile): static
    {

        $this->avatarFile = $avatarFile;

        if (null != $avatarFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    public function setAvatarSize(?int $avatarSize): static
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }

    public function getTricountUsers(): Collection
    {
        return $this->tricountUsers;
    }

    public function addTricountUser(TricountUser $tricountUser): static
    {
        if (!$this->tricountUsers->contains($tricountUser)) {
            $this->tricountUsers->add($tricountUser);
            $tricountUser->setUser($this);
        }
        return $this;
    }

    public function removeTricountUser(TricountUser $tricountUser): static
    {
        if ($this->tricountUsers->removeElement($tricountUser)) {
            if ($tricountUser->getUser() === $this) {
                $tricountUser->setUser(null);
            }
        }
        return $this;
    }

    public function getTricounts(): Collection
    {
        $tricounts = new ArrayCollection();
        foreach ($this->tricountUsers as $tricountUser) {
            $tricounts->add($tricountUser->getTricount());
        }
        return $tricounts;
    }

    public function getOwnedTricounts(): Collection
    {
        $tricounts = new ArrayCollection();
        foreach ($this->tricountUsers as $tricountUser) {
            if ($tricountUser->isOwner()) {
                $tricounts->add($tricountUser->getTricount());
            }
        }
        return $tricounts;
    }

    public function getMemberTricounts(): Collection
    {
        $tricounts = new ArrayCollection();
        foreach ($this->tricountUsers as $tricountUser) {
            if (!$tricountUser->isOwner()) {
                $tricounts->add($tricountUser->getTricount());
            }
        }
        return $tricounts;
    }

    public function getTotalBalance(): float
    {
        $balance = 0;
        foreach ($this->tricounts as $tricount) {
            $balance += $this->getBalanceForTricount($tricount);
        }
        return $balance;
    }

    public function getBalanceForTricount(Tricount $tricount): float
    {
        $balance = 0;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getTricount() === $tricount) {
                $balance += $transaction->getAmount();
            }
        }
        return $balance;
    }

    public function getTotalUnpaidAmount(): float
    {
        $total = 0;
        foreach ($this->transactions as $transaction) {
            foreach ($transaction->getSplits() as $split) {
                if ($split->getUser() === $this && !$split->isPaid()) {
                    $total += $split->getAmount();
                }
            }
        }
        return $total;
    }

    public function getTotalPaidAmount(): float
    {
        $total = 0;
        foreach ($this->transactions as $transaction) {
            foreach ($transaction->getSplits() as $split) {
                if ($split->getUser() === $this && $split->isPaid()) {
                    $total += $split->getAmount();
                }
            }
        }
        return $total;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
