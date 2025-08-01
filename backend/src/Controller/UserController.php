<?php

namespace App\Controller;

use App\Entity\BoardMember;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user/me')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user', methods: ['GET'])]
    public function getMe(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $user = $this->getUser();

        // Debug: Log session info
        error_log('Session ID: ' . $request->getSession()->getId());
        error_log('User: ' . ($user ? $user->getEmail() : 'null'));
        error_log('Cookies: ' . json_encode($request->cookies->all()));

        if (!$user) {
            return new JsonResponse(['message'=>'Not Authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $jsonUser = $serializer->serialize($user, 'json', ['groups'=>'auth_registration']);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
