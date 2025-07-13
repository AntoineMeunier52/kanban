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
    #[Groups( ['auth_registration', 'get_details_board', 'get_members'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups( ['auth_registration', 'get_details_board', 'get_members'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups( ['auth_registration', 'get_details_board', 'get_members'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['auth_registration', 'get_details_board', 'get_members'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $hashPassword = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expirationDate = null;

    #[ORM\Column(options: ['default'=>false])]
    private ?bool $isVerified = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetPasswordExpiresAt = null;

    /**
     * @var Collection<int, BoardMember>
     */
    #[ORM\OneToMany(targetEntity: BoardMember::class, mappedBy: 'user')]
    private Collection $memberships;

    /**
     * @var Collection<int, Board>
     */
    #[ORM\OneToMany(targetEntity: Board::class, mappedBy: 'owner')]
    private Collection $ownedBoards;

    public function __construct()
    {
        $this->memberships = new ArrayCollection();
        $this->ownedBoards = new ArrayCollection();
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

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordExpiresAt(): ?\DateTimeImmutable
    {
        return $this->resetPasswordExpiresAt;
    }

    public function setResetPasswordExpiresAt(?\DateTimeImmutable $resetPasswordExpiresAt): static
    {
        $this->resetPasswordExpiresAt = $resetPasswordExpiresAt;

        return $this;
    }

    // -------------------RELATION-----------------------------------

    /**
     * @return Collection<int, BoardMember>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(BoardMember $membership): static
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setUser($this);
        }

        return $this;
    }

    public function removeMembership(BoardMember $membership): static
    {
        if ($this->memberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getUser() === $this) {
                $membership->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Board>
     */
    public function getOwnedBoards(): Collection
    {
        return $this->ownedBoards;
    }

    public function addOwnedBoard(Board $board): static
    {
        if (!$this->ownedBoards->contains($board)) {
            $this->ownedBoards->add($board);
            $board->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedBoard(Board $board): static
    {
        if ($this->ownedBoards->removeElement($board)) {
            // set the owning side to null (unless already changed)
            if ($board->getOwner() === $this) {
                $board->setOwner(null);
            }
        }

        return $this;
    }
}
