<?php

namespace App\PackTrack\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderCommand
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }

    public function getReference(): string
    {
        return $this->reference;
    }
}
