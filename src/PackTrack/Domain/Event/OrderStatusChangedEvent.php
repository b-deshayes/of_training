<?php

declare(strict_types=1);

namespace App\PackTrack\Domain\Event;

class OrderStatusChangedEvent
{
    private string $orderReference;
    private string $oldStatusName;
    private string $newStatusName;

    public function __construct(
        string $orderReference,
        string $oldStatusName,
        string $newStatusName
    ) {
        $this->orderReference = $orderReference;
        $this->oldStatusName = $oldStatusName;
        $this->newStatusName = $newStatusName;
    }

    public function getOrderReference(): string
    {
        return $this->orderReference;
    }

    public function getOldStatusName(): string
    {
        return $this->oldStatusName;
    }

    public function getNewStatusName(): string
    {
        return $this->newStatusName;
    }
}
