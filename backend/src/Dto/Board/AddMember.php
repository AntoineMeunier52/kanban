<?php

namespace App\Dto\Board;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\BoardRole;
class AddMember {
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\NotNull(message: 'Email cannot be Null')]
    #[Assert\Email(message: 'The email \'{{ value }}\' is not a valid email.')]
    public string $email;

    //role verification in BoardControler /{id}/member POST
    #[Assert\NotBlank(message:'Role is required')]
    public string $role;
}
