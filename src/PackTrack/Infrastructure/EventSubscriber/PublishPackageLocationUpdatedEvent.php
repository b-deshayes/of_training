<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\PackageLocationUpdatedEvent;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class PublishPackageLocationUpdatedEvent
{
    public function __construct(
        private HubInterface $mercureHub,
        private PackageRepositoryInterface $packageRepository,
        private SerializerInterface $serializer
    ) {}

    public function __invoke(PackageLocationUpdatedEvent $event): void
    {
        $package = $this->packageRepository->findById($event->getPackageId());
        if (!$package) {
            return;
        }

        $update = new Update(
            sprintf('order/%s', $package->getOrder()->getId()),
            json_encode([
                'type' => 'package.location_updated',
                'data' => [
                    'packageId' => $event->getPackageId(),
                    'trackingNumber' => $package->getTrackingNumber(),
                    'location' => [
                        'latitude' => $event->getLatitude(),
                        'longitude' => $event->getLongitude(),
                        'timestamp' => $event->getTimestamp()->format(\DateTimeInterface::RFC3339),
                    ]
                ]
            ])
        );

        $this->mercureHub->publish($update);
    }
}
