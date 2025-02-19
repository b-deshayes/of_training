<?php

namespace App\PackTrack\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrderStatusCommandDTO
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private readonly string $status;

    public function __construct(
        string $status
    ) {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
