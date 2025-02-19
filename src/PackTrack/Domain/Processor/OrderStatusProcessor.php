<?php

namespace App\PackTrack\Domain\Processor;

use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Entity\OrderStatusHistory;
use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use App\PackTrack\Domain\Repository\OrderStatusHistoryRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderStatusProcessor
{
    public function __construct(
        private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        private MessageBusInterface $eventBus
    ) {
    }

    public function changeOrderStatus(Order $order, OrderStatus $newStatus): void
    {
        $currentStatus = $order->getStatus();

        if ($currentStatus === $newStatus) {
            return; // No change needed
        }

        $order->setStatus($newStatus); // Set new status on Order entity

        $statusHistory = new OrderStatusHistory($order, $newStatus);
        $this->orderStatusHistoryRepository->save($statusHistory);

        // Dispatch Domain Event
        $event = new OrderStatusChangedEvent(
            $order->getReference(),
            $currentStatus->getName(),
            $newStatus->getName()
        );
        $this->eventBus->dispatch($event);
    }
}
