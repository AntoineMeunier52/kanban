<?php

namespace App\Controller;

use App\Dto\Board\AddMember;
use App\Dto\Board\CreateBoard;
use App\Dto\Board\UpdateBoard;
use App\Entity\Board;
use App\Entity\BoardMember;
use App\Entity\Invit;
use App\Entity\User;
use App\Enum\BoardRole;
use App\Repository\BoardMemberRepository;
use App\Repository\BoardRepository;
use App\Repository\InvitRepository;
use App\Repository\UserRepository;
use App\Security\Voter\BoardVoter;
use App\Service\BoardService;
use App\Service\EmailSender;
use App\Utils\FormatValidatorError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/boards')]
final class BoardController extends AbstractController
{

    /////////////////////////////////////////////////////////////////////////////////
    //BOARDS
    /////////////////////////////////////////////////////////////////////////////////

    #[Route(path: '', name: 'create_board', methods: ['POST'])]
    public function createBoard(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        UserInterface $user
        ): JsonResponse
    {
        $boardData = $serializer->deserialize($request->getContent(), CreateBoard::class, 'json');

        $errors = $validator->validate($boardData);
        if ($errors->count() > 0 && $request->getContent()) {
            return FormatValidatorError::sendMessages($errors);
        }

        $board = new Board();
        $board->setName($boardData->name);
        $board->setOwner($user);

        $membership = new BoardMember();
        $membership->setBoard($board);
        $membership->setUser($user);
        $membership->setRole(BoardRole::ADMIN);

        $em->persist($membership);
        $em->persist($board);
        $em->flush();

        $jsonBoard = $serializer->serialize($board, 'json', ['groups'=>'get_details_board']);
        $location = $urlGenerator->generate('get_board_by_id', ['id' => $board->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBoard, Response::HTTP_CREATED, ['Location'=>$location], true);
    }

    #[Route(path: '', name: 'get_all_boards', methods: ['GET'])]
    public function getBoards(SerializerInterface $serializer, UserInterface $user): JsonResponse
    {
        $memberships = $user->getMemberships();

        $boards = array_map(
            fn(BoardMember $membership) => $membership->getBoard(),
            $memberships->toArray()
        );

        $boards = array_filter($boards, fn(Board $board) => !$board->isDeleted());

        $jsonBoards = $serializer->serialize($boards,'json', ['groups'=> 'get_board']);
        return new JsonResponse($jsonBoards, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/{id}', name: 'get_board_by_id', methods: ['GET'])]
    #[IsGranted(attribute: BoardVoter::VIEW, subject:'board')]
    public function getBoardById(
        Board $board,
        SerializerInterface $serializer,
        BoardService $boardService
    ): JsonResponse {
        if (!$boardService->isMember($board, $this->getUser())) {
            return new JsonResponse(['message'=>'Acces denied'], Response::HTTP_FORBIDDEN);
        }

        $jsonBoard = $serializer->serialize($board, 'json', ['groups'=>'get_details_board']);
        return new JsonResponse($jsonBoard, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/{id}', name: 'update_board', methods: ['PATCH'])]

    public function updateBoard(
        Board $currentBoard,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UserInterface $user,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): JsonResponse {
        if ($currentBoard->getOwner() !== $user) {
            return new JsonResponse(['messages'=>'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        if ($currentBoard->isDeleted()) {
            return new JsonResponse(['message' => 'This board has been deleted'], Response::HTTP_GONE);
        }

        //add validator
        $boardDto = $serializer->deserialize($request->getContent(), UpdateBoard::class, 'json');

        $errors = $validator->validate($boardDto);
        if ($errors->count() > 0) {
            return FormatValidatorError::sendMessages($errors);
        }

        if ($boardDto->name) {
            $currentBoard->setName($boardDto->name);
        }

        if ($boardDto->owner) {
            $newOwner = $userRepository->find($boardDto->owner);
            if (!$newOwner) {
                return new JsonResponse(['message'=> 'New owner not found'], Response::HTTP_BAD_REQUEST);
            }
            $currentBoard->setOwner($newOwner);
        }

        $em->persist($currentBoard);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path:'/{id}', name:'delete_board', methods: ['DELETE'])]
    public function deleteBoard(
        Board $currentBoard,
        EntityManagerInterface $em,
        UserInterface $user
    ): JsonResponse {
        if ($currentBoard->getOwner() !== $user) {
            return new JsonResponse(['messages'=> 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $currentBoard->setIsDeleted(true);

        $em->persist($currentBoard);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /////////////////////////////////////////////////////////////////////////////////
    //BOARDS - MEMBERS
    /////////////////////////////////////////////////////////////////////////////////

    #[Route(path:'/{id}/members', name:'get_board_member', methods: ['GET'])]
    public function getAllMembers(
        Board $currentBoard,
        BoardMemberRepository $memberRepository,
        SerializerInterface $serializer,
        BoardService $boardService
    ): JsonResponse {
        $isMember = $boardService->isMember($currentBoard, $this->getUser());

        if (!$isMember) {
            return new JsonResponse(['message'=> 'Fobidden'], Response::HTTP_FORBIDDEN);
        }

        $members = $memberRepository->findBy(['board'=>$currentBoard]);

        $membersArray = array_map(function (BoardMember $memberShip) {
            $user = $memberShip->getUser();
            return [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'LastName' => $user->getLastName(),
                'Email' => $user->getEmail(),
                'Role' => $memberShip->getRole()->value
                ];
            }, $members
        );
        $memberjson = $serializer->serialize($membersArray, 'json');
        return new JsonResponse($memberjson, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/{id}/members', name:'add_board_member', methods: ['POST'])]
    #[IsGranted(BoardVoter::ADMIN, subject:'currentBoard')]
    public function addMembers(
        Board $currentBoard,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        BoardMemberRepository $memberRepository,
        UserRepository $userRepository,
        InvitRepository $invitRepository,
        ValidatorInterface $validator,
        EmailSender $emailSender
    ): JsonResponse {
        $new_member = $serializer->deserialize($request->getContent(), AddMember::class, 'json');

        $errors = $validator->validate($new_member);
        if (count($errors) > 0) {
            return FormatValidatorError::sendMessages($errors);
        }

        try {
            $roleEnum = BoardRole::from(strtolower($new_member->role));
        } catch (\ValueError) {
            return new JsonResponse(['message'=>'Invalid role'], Response::HTTP_BAD_REQUEST);
        }

        //get instant of the new member
        $userToAdd = $userRepository->findOneBy(['email' => $new_member->email]);
        $checkInvit = $invitRepository->findOneBy(['email'=> $new_member->email]);

        if ($checkInvit) {
            return new JsonResponse(['message'=> 'Invit already send'], Response::HTTP_FORBIDDEN);
        }

        if (!$userToAdd) {
            $uuid = Uuid::v7();
            //ici invite du nouvelle utilisateur
            $memberToAdd = new Invit();
            $memberToAdd->setEmail($new_member->email);
            $memberToAdd->setInvitCode($uuid);
            $memberToAdd->setBoard($currentBoard);
            $memberToAdd->setRole($roleEnum);

            $emailReplacer = ['link'=>"https://127.0.0.1:8000/invit?uuid=$uuid"];
            $emailSender->send(
                template: 'invitEmail.html',
                replacer: $emailReplacer,
                sendTo: $new_member->email,
                subject: 'kanban - invit'
            );

            $em->persist($memberToAdd);
            $em->flush();
            return new JsonResponse(['message'=> 'invit send successfuly'], Response::HTTP_OK);
        }

        //transform role request to role Enum

        //check if user is already member
        foreach ($currentBoard->getBoardMembers() as $member) {
            if ($member->getUser()->getId() === $userToAdd->getId()) {
                return new JsonResponse(['message'=>'User already member'], Response::HTTP_CONFLICT);
            }
        }

        $membership = new BoardMember();
        $membership->setBoard($currentBoard);
        $membership->setUser($userToAdd);
        $membership->setRole($roleEnum);

        $em->persist($membership);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
