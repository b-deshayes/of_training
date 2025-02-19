<?php

namespace App\PackTrack\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SimulatePackageLocationDTO
{
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $iterations;

    #[Assert\NotNull]
    #[Assert\Range(min: -90, max: 90)]
    private float $startLatitude;

    #[Assert\NotNull]
    #[Assert\Range(min: -180, max: 180)]
    private float $startLongitude;

    #[Assert\NotNull]
    #[Assert\Range(min: -90, max: 90)]
    private float $endLatitude;

    #[Assert\NotNull]
    #[Assert\Range(min: -180, max: 180)]
    private float $endLongitude;

    public function __construct(
        int $iterations,
        float $startLatitude,
        float $startLongitude,
        float $endLatitude,
        float $endLongitude
    ) {
        $this->iterations = $iterations;
        $this->startLatitude = $startLatitude;
        $this->startLongitude = $startLongitude;
        $this->endLatitude = $endLatitude;
        $this->endLongitude = $endLongitude;
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }

    public function getStartLatitude(): float
    {
        return $this->startLatitude;
    }

    public function getStartLongitude(): float
    {
        return $this->startLongitude;
    }

    public function getEndLatitude(): float
    {
        return $this->endLatitude;
    }

    public function getEndLongitude(): float
    {
        return $this->endLongitude;
    }
}
