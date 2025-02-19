<?php

namespace Tests\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\UpdateOrderStatusCommand;
use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderStatusRepositoryInterface;
use App\PackTrack\Domain\Processor\OrderStatusProcessor;
use App\PackTrack\Domain\Exception\OrderNotFoundException;
use App\PackTrack\Domain\Exception\OrderStatusNotFoundException;
use App\PackTrack\Application\CommandHandler\UpdateOrderStatusCommandHandler;


use PHPUnit\Framework\TestCase;

class UpdateOrderStatusCommandHandlerTest extends TestCase
{
    public function testUpdateOrderStatusSuccessfully(): void
    {
        $orderId = 1;
        $statusId = 2;
        $command = new UpdateOrderStatusCommand($orderId, $statusId);

        $order = new Order('REF-123');
        $initialStatus = new OrderStatus('Created');
        $order->setStatus($initialStatus);
        $newStatus = new OrderStatus('Shipped');
        $order->setId($orderId);
        $newStatus->setId($statusId);

        // Mock du OrderRepository
        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($orderId)
            ->willReturn($order);
            $orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Order $savedOrder) use ($newStatus) {
                return $savedOrder->getStatus()->getName() === $newStatus->getName();
            }));

        // Mock du OrderStatusRepository
        $orderStatusRepositoryMock = $this->createMock(OrderStatusRepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($statusId)
            ->willReturn($newStatus);

        // Mock du OrderStatusProcessor
        $orderStatusProcessorMock = $this->createMock(OrderStatusProcessor::class);
        $orderStatusProcessorMock->expects($this->once())
            ->method('changeOrderStatus')
            ->willReturnCallback(function (Order $order, OrderStatus $newStatus) {
                $order->setStatus($newStatus); // Assurez-vous que le statut est mis à jour ici
            });

        $handler = new UpdateOrderStatusCommandHandler($orderRepositoryMock, $orderStatusRepositoryMock, $orderStatusProcessorMock);
        $handler->__invoke($command);
    }

    public function testUpdateOrderStatusOrderNotFound(): void
    {
        $orderId = 999;
        $statusId = 2;
        $command = new UpdateOrderStatusCommand($orderId, $statusId);

        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($orderId)
            ->willReturn(null);

        $orderStatusRepositoryMock = $this->createMock(OrderStatusRepositoryInterface::class);
        $orderStatusProcessorMock = $this->createMock(OrderStatusProcessor::class);

        $handler = new UpdateOrderStatusCommandHandler($orderRepositoryMock, $orderStatusRepositoryMock, $orderStatusProcessorMock);

        $this->expectException(OrderNotFoundException::class);
        $handler->__invoke($command);
    }

    public function testUpdateOrderStatusStatusNotFound(): void
    {
        $orderId = 1;
        $statusId = 999;
        $command = new UpdateOrderStatusCommand($orderId, $statusId);

        $order = new Order('REF-123');
        $order->setId($orderId);

        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($orderId)
            ->willReturn($order);

        $orderStatusRepositoryMock = $this->createMock(OrderStatusRepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->once())
            ->method('findById')
            ->with($statusId)
            ->willReturn(null);

        $orderStatusProcessorMock = $this->createMock(OrderStatusProcessor::class);

        $handler = new UpdateOrderStatusCommandHandler($orderRepositoryMock, $orderStatusRepositoryMock, $orderStatusProcessorMock);

        $this->expectException(OrderStatusNotFoundException::class);
        $handler->__invoke($command);
    }
}
