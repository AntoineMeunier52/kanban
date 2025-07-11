<?php

namespace App\Entity;

use App\Enum\BoardRole;
use App\Repository\InvitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InvitRepository::class)]
class Invit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(enumType: BoardRole::class)]
    private BoardRole $role;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $invitCode = null;

    #[ORM\ManyToOne(inversedBy: 'invits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?board $board = null;


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

        return $this;
    }

    public function getInvitCode(): Uuid
    {
        return $this->invitCode;
    }

    public function setInvitCode(Uuid $invitCode): static
    {
        $this->invitCode = $invitCode;

        return $this;
    }

    public function getRole(): BoardRole
    {
        return $this->role;
    }

    public function setRole(BoardRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getBoard(): ?board
    {
        return $this->board;
    }

    public function setBoard(?board $board): static
    {
        $this->board = $board;

        return $this;
    }
}
