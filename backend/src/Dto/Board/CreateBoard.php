<?php

namespace App\Dto\Board;

use Symfony\Component\Validator\Constraints as Assert;

class CreateBoard
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 1,  max: 48, minMessage:'The miminum size for board name is {{ limit }}', maxMessage:'The maximum size for board name is {{ limit }}')]
    public ?string $name = null;
}
