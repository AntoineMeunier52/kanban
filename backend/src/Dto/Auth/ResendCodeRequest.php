<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class ResendCodeRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}
