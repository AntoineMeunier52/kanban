<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class FormatValidatorError
{
    public static function sendMessages(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        return new JsonResponse(['errors'=>$messages], Response::HTTP_BAD_REQUEST);
    }
}
