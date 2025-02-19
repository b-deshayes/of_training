<?php

namespace Tests\PackTrack\Domain\Service;

use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Repository\OrderStatusHistoryRepositoryInterface;
use App\PackTrack\Domain\Processor\OrderStatusProcessor;
use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use PHPUnit\Framework\TestCase;


class OrderStatusProcessorTest extends TestCase
{
    public function testChangeOrderStatusSuccessfully(): void
    {
        $order = new Order('REF-123');
        $initialStatus = new OrderStatus('Created');
        $order->setStatus($initialStatus);
        $newStatus = new OrderStatus('Shipped');

        $orderStatusHistoryRepositoryMock = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $orderStatusHistoryRepositoryMock->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($statusHistory) use ($order, $newStatus) {
                $this->assertSame($order, $statusHistory->getOrder());
                $this->assertSame($newStatus, $statusHistory->getStatus());
                $order->addStatusHistory($statusHistory);
                $this->assertCount(1, $order->getStatusHistory());
            });

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock->expects($this->once())
            ->method('dispatch')
            ->willReturnCallback(function ($event) use ($order, $initialStatus, $newStatus) {
                $this->assertInstanceOf(OrderStatusChangedEvent::class, $event);
                $this->assertSame($order, $event->getOrder());
                $this->assertSame($initialStatus, $event->getOldStatus());
                $this->assertSame($newStatus, $event->getNewStatus());
                return new Envelope($event);
            });

        $this->assertInstanceOf(OrderStatusHistoryRepositoryInterface::class, $orderStatusHistoryRepositoryMock);
        $this->assertInstanceOf(MessageBusInterface::class, $eventBusMock);

        $orderStatusProcessor = new OrderStatusProcessor($orderStatusHistoryRepositoryMock, $eventBusMock);
        $orderStatusProcessor->changeOrderStatus($order, $newStatus);

        $this->assertSame($newStatus, $order->getStatus());
        $this->assertCount(1, $order->getStatusHistory());
    }

    public function testChangeOrderStatusNoChange(): void
    {
        $order = new Order('REF-123');
        $initialStatus = new OrderStatus('Created');
        $order->setStatus($initialStatus);

        $orderStatusHistoryRepositoryMock = $this->createMock(OrderStatusHistoryRepositoryInterface::class);
        $orderStatusHistoryRepositoryMock->expects($this->never())
            ->method('save');

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock->expects($this->never())
            ->method('dispatch');

        $this->assertInstanceOf(OrderStatusHistoryRepositoryInterface::class, $orderStatusHistoryRepositoryMock);
        $this->assertInstanceOf(MessageBusInterface::class, $eventBusMock);

        $orderStatusProcessor = new OrderStatusProcessor($orderStatusHistoryRepositoryMock, $eventBusMock);
        $orderStatusProcessor->changeOrderStatus($order, $initialStatus);

        $this->assertSame($initialStatus, $order->getStatus());
        $this->assertEmpty($order->getStatusHistory());
    }
}
