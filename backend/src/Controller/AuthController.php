<?php

namespace App\Controller;

use App\Dto\Auth\RegisterRequest;
use App\Dto\Auth\VerifyEmailRequest;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\User;

#[Route('/api/auth')]
final class AuthController extends AbstractController
{
    #[Route('/register', name: 'auth_register', methods: ['POST'])]
    public function authRegister(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), RegisterRequest::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0 && $request->getContent()) {
            $errorMessage = [];
            foreach ($errors as $error) {
                $errorMessage[] = $error->getMessage();
            }
            return new JsonResponse(['errors'=> $errorMessage], Response::HTTP_BAD_REQUEST, []);
        }

        if ($userRepository->findOneBy(['email' => $user->email])) {
            return new JsonResponse(['message' => 'Email is already in use.'], Response::HTTP_CONFLICT, []);
        }

        $userObj = new User();
        $userObj->setFirstName($user->firstName);
        $userObj->setLastName($user->lastName);
        $userObj->setEmail($user->email);
        $userObj->setHashPassword($hasher->hashPassword($userObj, $user->password));
        $userObj->setVerificationCode((string) random_int(100000, 999999));
        $userObj->setIsVerified(false);
        $userObj->setExpirationDate(new \DateTimeImmutable('+15 minutes'));

        $em->persist($userObj);
        $em->flush();

        $jsonUser = $serializer->serialize($userObj, 'json', ['groups'=>'auth_registration']);
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }

    #[Route('/verify-email', name: 'auth_verify_email', methods: ['POST'])]
    public function verifyEmail(
        Request $request,
        SerializerInterface $serialiser,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse
    {
        $userData = $serialiser->deserialize($request->getContent(), VerifyEmailRequest::class, 'json');

        $errors = $validator->validate($userData);
        if ($errors->count() > 0) {
            $message = [];
            foreach ($errors as $error) {
                $message[] = $error->getMessage();
            }
            return new JsonResponse(['errors'=>$message], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email'=> $userData->email]);

        if (!$user || $user->getVerificationCode() !== $userData->code) {
            return new JsonResponse(['message'=>'Invalid email or verification code.'], Response::HTTP_BAD_REQUEST);
        }

        if ($user->getExpirationDate() < new \DateTimeImmutable()) {
            return new JsonResponse(['message'=>'Verification code Expired.'], Response::HTTP_GONE);
        }

        $user->setIsVerified(true);
        $user->setVerificationCode(null);
        $user->setExpirationDate(null);

        //no persist is needed because the resource is handle by the entity manager with repository
        $em->flush();
        return new JsonResponse(['message'=> 'Email verified succesfully'], Response::HTTP_OK);
    }
}
