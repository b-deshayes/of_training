<?php

namespace App\PackTrack\Application\Command;

class SimulatePackageLocationCommand
{
    public function __construct(
        private string $trackingNumber,
        private int $iterations,
        private float $startLatitude,
        private float $startLongitude,
        private float $endLatitude,
        private float $endLongitude,
        private int $currentIteration = 0
    ) {}

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }

    public function getCurrentIteration(): int
    {
        return $this->currentIteration;
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
