<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyEmailRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d{6}$/', message: 'Invalid verification code.')]
    public ?string $code = null;
}
