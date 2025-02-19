<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\PackageCreatedEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class PublishPackageCreatedEvent
{
    public function __construct(
        private HubInterface $mercureHub,
        private PackageRepositoryInterface $packageRepository,
        private SerializerInterface $serializer
    ) {
    }

    public function __invoke(PackageCreatedEvent $event): void
    {
        $package = $this->packageRepository->findById($event->getPackageId());
        if (!$package) {
            return;
        }
        $update = new Update(
            sprintf('order/%s', $event->getOrderId()),
            json_encode([
                'type' => 'package.created',
                'package' => $this->serializer->serialize($package, 'json', ['groups' => ['package:read']])
            ])
        );
        $this->mercureHub->publish($update);
    }
}
