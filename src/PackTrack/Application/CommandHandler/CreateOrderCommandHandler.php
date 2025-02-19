<?php

namespace App\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\CreateOrderCommand;
use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderStatusRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrderCommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderStatusRepositoryInterface $orderStatusRepository,
    ) {
    }

    public function __invoke(CreateOrderCommand $command): void
    {
        $order = new Order($command->getReference());
        $createdStatus = $this->orderStatusRepository->findByName('CREATED');
        $order->setStatus($createdStatus);
        $this->orderRepository->save($order);
    }
}
