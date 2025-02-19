<?php

namespace App\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\CreatePackageCommand;
use App\PackTrack\Domain\Entity\Package;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use App\PackTrack\Domain\Event\PackageCreatedEvent;

#[AsMessageHandler]
class CreatePackageCommandHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PackageRepositoryInterface $packageRepository,
        private MessageBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreatePackageCommand $command): void
    {
        $order = $this->orderRepository->findById($command->getOrderId());
        if (!$order) {
            throw new \Exception('Order not found');
        }

        $package = new Package($command->getTrackingNumber(), $order);
        $this->packageRepository->save($package);

        $event = new PackageCreatedEvent($package->getId(), $order->getId(), $command->getTrackingNumber());
        $this->eventBus->dispatch($event);
    }
}
