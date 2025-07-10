<?php

namespace App\Security\Voter;

use App\Entity\BoardMember;
use App\Enum\BoardRole;
use App\Repository\BoardMemberRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class BoardVoter extends Voter
{
    public const VIEW = "VIEW";
    public const EDIT = 'EDIT';
    public const ADMIN = 'ADMIN';
    public const OWNER = 'OWNER';


    public function __construct(private BoardMemberRepository $boardMemberRepository){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OWNER, self::ADMIN, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Board;
    }

    protected function voteOnAttribute(string $attribute, mixed $board, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($board->isDeleted()) {
            return false;
        }

        if ($attribute === self::OWNER) {
            return $board->getOwner() === $user;
        }

        $membership = $this->boardMemberRepository->findOneBy([
            'board'=>$board,
            'user'=>$user
        ]);

        if (!$membership) {
            return false;
        }

        $role = $membership->getRole();
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::VIEW:
                return true;
            case self::EDIT:
                if ($role === BoardRole::WRITE || $role === BoardRole::ADMIN) {
                    return true;
                }
                break;

            case self::ADMIN:
                if ($role === BoardRole::ADMIN) {
                    return true;
                }
                break;
            default:
                return false;
        }
        return false;
    }
}
