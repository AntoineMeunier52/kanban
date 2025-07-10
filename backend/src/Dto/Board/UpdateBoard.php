<?php

namespace App\Dto\Board;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateBoard
{
    #[Assert\Length(min: 1, max: 50)]
    public ?string $name = null;

    #[Assert\Positive]
    public ?int $owner = null;
}
