<?php

namespace Tests\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\CreateOrderCommand;
use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderStatusRepositoryInterface;
use App\PackTrack\Application\CommandHandler\CreateOrderCommandHandler;
use PHPUnit\Framework\TestCase;

class CreateOrderCommandHandlerTest extends TestCase
{
    public function testCreateOrderSuccessfully(): void
    {
        $command = new CreateOrderCommand('REF-TEST');

        // Mock du OrderRepository
        $orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $orderRepositoryMock->expects($this->once()) // Vérifie que save() est appelé une seule fois
            ->method('save')
            ->willReturnCallback(function (Order $order) {
                $this->assertEquals('REF-TEST', $order->getReference()); // Vérifie la référence de la commande sauvegardée
                $this->assertEquals('Created', $order->getStatus()->getName()); // Vérifie le statut 'Created'
            });

        // Mock du OrderStatusRepository
        $orderStatusRepositoryMock = $this->createMock(OrderStatusRepositoryInterface::class);
        $orderStatusRepositoryMock->expects($this->once()) // Vérifie que findByName() est appelé une seule fois
            ->method('findByName')
            ->with('Created') // Vérifie que findByName est appelé avec 'Created'
            ->willReturn(null); // Simule que le statut 'Created' n'existe pas en base (pour tester la création)
        $orderStatusRepositoryMock->expects($this->once()) // Vérifie que save() est appelé une seule fois pour OrderStatus
            ->method('save')
            ->willReturnCallback(function (OrderStatus $status) {
                $this->assertEquals('Created', $status->getName()); // Vérifie que le statut 'Created' est sauvegardé
            });

        $handler = new CreateOrderCommandHandler($orderRepositoryMock, $orderStatusRepositoryMock);
        $handler->__invoke($command);

        // Pas d'assertion explicite de retour, car le handler est void. Les assertions sont dans les mocks.
    }
}
