<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordRequest
{
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "Please provide a valid email address")]
    public string $email;
}