<?php

namespace App\PackTrack\Application\Command;

class CreatePackageCommand
{
    private int $orderId;
    private string $trackingNumber;

    public function __construct(int $orderId, string $trackingNumber)
    {
        $this->orderId = $orderId;
        $this->trackingNumber = $trackingNumber;
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
