<?php

namespace App\Dto\Auth;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
class InvitRequest
{
    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-]+$/u', message: 'The first name can only contain letters, spaces and hyphens.')]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: "Last name is required.")]
    #[Assert\Regex(pattern: '/^[\p{L}\s\-]+$/u', message: 'The Last name can only contain letters, spaces and hyphens.')]
    public ?string $lastName = null;

    #[Assert\NotBlank(message: 'password can\'t be blank')]
    #[Assert\Length(min: 8, minMessage:'The minimum size for password is {{ limit }}')]
    public ?string $password = null;

    #[Assert\NotBlank(message: "uuid is required")]
    #[Assert\Uuid(message:"not a valid uuid")]
    public Uuid $uuid;
}
