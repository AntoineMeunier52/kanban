<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups( ['auth_registration'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups( ['auth_registration'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups( ['auth_registration'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['auth_registration'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $hashPassword = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expirationDate = null;

    #[ORM\Column(options: ['default'=>false])]
    private ?bool $isVerified = false;

    /**
     * @var Collection<int, BoardMember>
     */
    #[ORM\OneToMany(targetEntity: BoardMember::class, mappedBy: 'userId')]
    private Collection $boardMember;

    /**
     * @var Collection<int, Board>
     */
    #[ORM\OneToMany(targetEntity: Board::class, mappedBy: 'ownerId')]
    private Collection $boardsId;

    public function __construct()
    {
        $this->boardMember = new ArrayCollection();
        $this->boardsId = new ArrayCollection();
    }


    //security method
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return $this->hashPassword;
    }

    public function eraseCredentials(): void
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getHashPassword(): ?string
    {
        return $this->hashPassword;
    }

    public function setHashPassword(string $hashPassword): static
    {
        $this->hashPassword = $hashPassword;

        return $this;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): static
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, BoardMember>
     */
    public function getBoardMember(): Collection
    {
        return $this->boardMember;
    }

    public function addBoardMember(BoardMember $boardId): static
    {
        if (!$this->boardMember->contains($boardId)) {
            $this->boardMember->add($boardId);
            $boardId->setUser($this);
        }

        return $this;
    }

    public function removeBoardId(BoardMember $boardMemberId): static
    {
        if ($this->boardMember->removeElement($boardMemberId)) {
            // set the owning side to null (unless already changed)
            if ($boardMemberId->getUser() === $this) {
                $boardMemberId->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Board>
     */
    public function getBoardsId(): Collection
    {
        return $this->boardsId;
    }

    public function addBoardsId(Board $boardsId): static
    {
        if (!$this->boardsId->contains($boardsId)) {
            $this->boardsId->add($boardsId);
            $boardsId->setOwner($this);
        }

        return $this;
    }

    public function removeBoardsId(Board $boardsId): static
    {
        if ($this->boardsId->removeElement($boardsId)) {
            // set the owning side to null (unless already changed)
            if ($boardsId->getOwner() === $this) {
                $boardsId->setOwner(null);
            }
        }

        return $this;
    }
}
