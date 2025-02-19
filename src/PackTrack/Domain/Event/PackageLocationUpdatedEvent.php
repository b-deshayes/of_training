<?php

namespace App\PackTrack\Domain\Event;

class PackageLocationUpdatedEvent
{
    public function __construct(
        private int $packageId,
        private float $latitude,
        private float $longitude,
        private \DateTimeImmutable $timestamp
    ) {}

    public function getPackageId(): int
    {
        return $this->packageId;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }
}
