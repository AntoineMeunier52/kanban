<?php

namespace App\Controller;

use App\Dto\Board\CreateBoard;
use App\Entity\Board;
use App\Entity\BoardMember;
use App\Enum\BoardRole;
use App\Utils\FormatValidatorError;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/boards')]
final class BoardController extends AbstractController
{
    #[Route(path: '', name: 'create_board', methods: ['POST'])]
    public function createBoards(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();

        if(!$user) {
            return new JsonResponse(['message'=> 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

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
    public function getBoards(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message'=> 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $memberships = $user->getMemberships();

        $board = array_map(fn(BoardMember $membership) => $membership->getBoard(), $memberships->toArray());

        $jsonBoards = $serializer->serialize($board,'json', ['groups'=> 'get_board']);
        return new JsonResponse($jsonBoards, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/{id}', name: 'get_board_by_id', methods: ['GET'])]
    public function getBoardById(
        Board $board,
        SerializerInterface $serializer,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message'=> 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $jsonBoard = $serializer->serialize($board, 'json', ['groups'=>'get_details_board']);
        return new JsonResponse($jsonBoard, Response::HTTP_OK, [], true);
    }
}
