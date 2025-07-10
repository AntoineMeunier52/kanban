<?php

namespace App\Service;

use App\Entity\Board;
use App\Entity\BoardMember;
use App\Entity\User;
use App\Enum\BoardRole;
use App\Repository\BoardMemberRepository;

class BoardService
{
    public function __construct(
        private BoardMemberRepository $boardMemberRepository,
    ) {}

    private function getMemberShip(Board $board, User $user): ?BoardMember {
        return $this->boardMemberRepository->findOneBy(
            ['user'=>$user,
            'board'=>$board]
        );
    }
    public function isMember (Board $board, User $user): bool {
        return $this->getMemberShip($board, $user) !== null;
    }

    public function getMemberRole (Board $board, User $user): ?BoardRole {
        return $this->getMemberShip($board, $user)?->getRole();
    }
}
