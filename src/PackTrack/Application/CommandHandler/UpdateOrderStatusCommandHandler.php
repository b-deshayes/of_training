<?php

namespace App\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\UpdateOrderStatusCommand;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderStatusRepositoryInterface;
use App\PackTrack\Domain\Processor\OrderStatusProcessor;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\PackTrack\Domain\Exception\OrderNotFoundException;
use App\PackTrack\Domain\Exception\OrderStatusNotFoundException;

#[AsMessageHandler]
class UpdateOrderStatusCommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderStatusRepositoryInterface $orderStatusRepository,
        private OrderStatusProcessor $orderStatusProcessor
    ) {
    }

    public function __invoke(UpdateOrderStatusCommand $command): void
    {
        $order = $this->orderRepository->findById($command->getOrderId());
        if (!$order) {
            throw new OrderNotFoundException(sprintf('Order with id "%s" not found', $command->getOrderId()));
        }

        $newStatus = $this->orderStatusRepository->findByName($command->getStatus());
        if (!$newStatus) {
            throw new OrderStatusNotFoundException(sprintf('OrderStatus with name "%s" not found', $command->getStatus()));
        }

        $this->orderStatusProcessor->changeOrderStatus($order, $newStatus);
        $this->orderRepository->save($order);
    }
}
