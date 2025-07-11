<?php

namespace App\Controller;

use App\Dto\Auth\InvitRequest;
use App\Dto\Auth\RegisterRequest;
use App\Dto\Auth\ResendCodeRequest;
use App\Dto\Auth\VerifyEmailRequest;
use App\Entity\BoardMember;
use App\Repository\InvitRepository;
use App\Repository\UserRepository;
use App\Service\EmailSender;
use App\Utils\FormatValidatorError;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authenticator\JsonLoginAuthenticator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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
        EmailSender $emailSender
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), RegisterRequest::class, 'json');

        $errors = $validator->validate($user);
        if ($errors->count() > 0 && $request->getContent()) {
            return FormatValidatorError::sendMessages($errors);
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
        $userObj->setExpirationDate(new DateTimeImmutable('+15 minutes'));

        $emailReplacer = ['firstName'=>$userObj->getFirstName(), 'verificationCode'=>$userObj->getVerificationCode()];
        $emailSender->send(
            template: 'emailVerification.html',
            replacer: $emailReplacer,
            sendTo: $userObj->getEmail(),
            subject: 'kanban - verification email'
        );


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
        UserRepository $userRepository,
        UserAuthenticatorInterface $userAuthenticator,
        #[Autowire(service: 'security.authenticator.json_login.main')]
        JsonLoginAuthenticator $authenticator,
        EmailSender $emailSender
    ): JsonResponse
    {
        $userData = $serialiser->deserialize($request->getContent(), VerifyEmailRequest::class, 'json');

        $errors = $validator->validate($userData);
        if ($errors->count() > 0) {
            return FormatValidatorError::sendMessages($errors);
        }

        $user = $userRepository->findOneBy(['email'=> $userData->email]);

        if (!$user || $user->getVerificationCode() !== $userData->code) {
            return new JsonResponse(['message'=>'Invalid email or verification code.'], Response::HTTP_BAD_REQUEST);
        }

        if ($user->getExpirationDate() < new DateTimeImmutable()) {
            return new JsonResponse(['message'=>'Verification code Expired.'], Response::HTTP_GONE);
        }

        $user->setIsVerified(true);
        $user->setVerificationCode(null);
        $user->setExpirationDate(null);

        $emailReplacer = ['firstName'=>$user->getFirstName()];
        $emailSender->send(
            template: 'succesVerification.html',
            replacer: $emailReplacer,
            sendTo: $user->getEmail(),
            subject: 'kanban - verification email'
        );

        //no persist is needed because the resource is handle by the entity manager with repository
        $em->flush();
        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
    }

    #[Route('/resend-code', name: 'auth_resend_code', methods: ['POST'])]
    public function resendVerificationCode(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        MailerInterface $mailer,
        EmailSender $emailSender
    ): JsonResponse
    {
        $userData = $serializer->deserialize($request->getContent(), ResendCodeRequest::class, 'json');

        $errors = $validator->validate($userData);
        if ($errors->count() > 0) {
            return FormatValidatorError::sendMessages($errors);
        }

        $user = $userRepository->findOneBy(['email'=> $userData->email]);

        if (!$user || $user->isVerified()) {
            return new JsonResponse(['message'=>'User not found or already verified.']);
        }

        $newCode = (string) random_int(100000, 999999);
        $user->setVerificationCode($newCode);
        $user->setExpirationDate(new DateTimeImmutable('+15 minutes'));

        $emailReplacer = ['firstName'=>$user->getFirstName(), 'verificationCode'=>$user->getVerificationCode()];
        $emailSender->send(
            template: 'resendVerification.html',
            replacer: $emailReplacer,
            sendTo: $user->getEmail(),
            subject: 'kanban - verification email'
        );

        $em->flush();

        return new JsonResponse(['message'=> 'New verification code send'], Response::HTTP_OK);
    }

    #[Route('/login', name: 'auth_login', methods: ['POST'])]
    public function login(): void
    {
        // Symfony Security intercepte cette route via json_login
    }

    #[Route('/logout', name: 'auth_logout', methods: ['POST'])]
    public function logout(): void
    {
        // Symfony Security intercepte cette route via json_logout
    }

    #[Route('/logout/success', name: 'auth_logout_success', methods: ['GET'])]
    public function logoutSuccess(): JsonResponse
    {
        return new JsonResponse(['message' => 'Logged out successfully.'], Response::HTTP_OK);
    }

    #[Route('/invite', name: 'auth_invite', methods: ['POST'])]
    public function invite(
        Request $request,
        InvitRepository $invitRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
     ): JsonResponse {
        //dd($request->getContent());
        $invit = $serializer->deserialize($request->getContent(), InvitRequest::class, 'json');
        $errors = $validator->validate($invit);
        if (count($errors) > 0) {
            return FormatValidatorError::sendMessages($errors);
        }

        $new_user = $invitRepository->findOneBy(['invitCode'=>$invit->uuid]);
        if (!$new_user) {
            return new JsonResponse(['message'=> 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $userObj = new User();
        $userObj->setFirstName($invit->firstName);
        $userObj->setLastName($invit->lastName);
        $userObj->setEmail($new_user->getEmail());
        $userObj->setHashPassword($hasher->hashPassword($userObj, $invit->password));
        $userObj->setIsVerified(true);

        $memberObj = new BoardMember();
        $memberObj->setBoard($new_user->getBoard());
        $memberObj->setRole($new_user->getRole());
        $memberObj->setUser($userObj);

        $em->remove($new_user);
        $em->persist($userObj);
        $em->persist($memberObj);
        $em->flush();

        return new JsonResponse(['message'=> 'user create'], Response::HTTP_OK);
    }
}
