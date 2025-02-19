<?php

namespace App\PackTrack\Domain\Event;

class PackageCreatedEvent
{
    private int $packageId;
    private int $orderId;
    private string $trackingNumber;

    public function __construct(int $packageId, int $orderId, string $trackingNumber)
    {
        $this->packageId = $packageId;
        $this->orderId = $orderId;
        $this->trackingNumber = $trackingNumber;
    }

    public function getPackageId(): int
    {
        return $this->packageId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }
}
