<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class OrderChangedEventListener
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private OrderRepositoryInterface $orderRepository,
        private HubInterface $mercureHub,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(OrderStatusChangedEvent $event): void
    {
        $order = $this->orderRepository->findByReference($event->getOrderReference());
        if (!$order) {
            return;
        }

        $update = new Update(
            sprintf('order/%s', $order->getId()),
            json_encode([
                'type' => 'order.status_changed',
                'order' => $this->serializer->serialize($order, 'json', ['groups' => ['order:read']])
            ])
        );
        $this->mercureHub->publish($update);
    }
}

