<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-]+$/u', message: 'The first name can only contain letters, spaces and hyphens.')]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: "Last name is required.")]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-]+$/u', message: 'The Last name can only contain letters, spaces and hyphens.')]
    public ?string $lastName = null;

    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'password can\'t be blank')]
    #[Assert\Length(min: 8, minMessage:'The minimum size for password is {{ limit }}')]
    public ?string $password = null;
}
