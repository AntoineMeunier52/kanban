<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
final class UserController extends AbstractController
{
    #[Route('/me', name: 'app_user', methods: ['GET'])]
    public function getMe(SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message'=>'Not Authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups'=>'auth_registration']);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
