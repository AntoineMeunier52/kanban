<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
class Board
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'boardsId')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, BoardMember>
     */
    #[ORM\OneToMany(targetEntity: BoardMember::class, mappedBy: 'boardId')]
    private Collection $boardMembers;

    public function __construct()
    {
        $this->boardMembers = new ArrayCollection();
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, BoardMember>
     */
    public function getBoardMembers(): Collection
    {
        return $this->boardMembers;
    }

    public function addBoardMember(BoardMember $boardMember): static
    {
        if (!$this->boardMembers->contains($boardMember)) {
            $this->boardMembers->add($boardMember);
            $boardMember->setBoard($this);
        }

        return $this;
    }

    public function removeBoardMember(BoardMember $boardMember): static
    {
        if ($this->boardMembers->removeElement($boardMember)) {
            // set the owning side to null (unless already changed)
            if ($boardMember->getBoard() === $this) {
                $boardMember->setBoard(null);
            }
        }

        return $this;
    }
}
