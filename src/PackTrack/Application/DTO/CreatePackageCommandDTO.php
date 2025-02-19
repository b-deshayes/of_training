<?php

namespace App\PackTrack\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePackageCommandDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $trackingNumber;

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }
}
