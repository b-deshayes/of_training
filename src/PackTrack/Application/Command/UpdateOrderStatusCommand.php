<?php

namespace App\PackTrack\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UpdateOrderStatusCommand
{
    #[Assert\Positive]
    private int $orderId;

    #[Assert\NotBlank]
    private string $status;

    public function __construct(int $orderId, string $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}

