<?php

namespace App\Entity;

use App\Enum\BoardRole;
use App\Repository\BoardMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BoardMemberRepository::class)]
class BoardMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_details_board'])]
    private ?int $id = null;

    #[ORM\Column(enumType: BoardRole::class)]
    #[Groups(['get_details_board', 'get_members'])]
    private BoardRole $role;

    #[ORM\ManyToOne(inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(groups: ['get_board', 'get_details_board', 'get_members'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'boardMembers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Board $board = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): static
    {
        $this->board = $board;

        return $this;
    }
}
