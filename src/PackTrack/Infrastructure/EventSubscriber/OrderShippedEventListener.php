<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Application\Command\CreatePackageCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class OrderShippedEventListener
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function __invoke(OrderStatusChangedEvent $event): void
    {
        if ($event->getNewStatusName() !== 'SHIPPED') {
            return;
        }

        $order = $this->orderRepository->findByReference($event->getOrderReference());
        if (!$order) {
            return;
        }

        $command = new CreatePackageCommand(
            $order->getId(),
            $this->generateRandomTrackingNumber()
        );

        $this->commandBus->dispatch($command);
    }

    private function generateRandomTrackingNumber(): string
    {
        return 'PT-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}

