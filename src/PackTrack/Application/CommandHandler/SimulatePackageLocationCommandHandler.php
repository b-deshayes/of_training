<?php

namespace App\PackTrack\Application\CommandHandler;

use App\PackTrack\Application\Command\SimulatePackageLocationCommand;
use App\PackTrack\Domain\Entity\PackageLocation;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use App\PackTrack\Domain\Exception\PackageNotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\PackTrack\Domain\Event\PackageLocationUpdatedEvent;
use App\PackTrack\Domain\Repository\PackageLocationRepositoryInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

#[AsMessageHandler]
class SimulatePackageLocationCommandHandler
{
    public function __construct(
        private PackageRepositoryInterface $packageRepository,
        private PackageLocationRepositoryInterface $packageLocationRepository,
        private MessageBusInterface $commandBus,
        private MessageBusInterface $eventBus
    ) {}

    public function __invoke(SimulatePackageLocationCommand $command): void
    {
        $package = $this->packageRepository->findByTrackingNumber($command->getTrackingNumber());
        if (!$package) {
            throw new PackageNotFoundException();
        }

        // Vérifier si la simulation doit se terminer
        if ($command->getCurrentIteration() >= $command->getIterations()) {
            return;
        }

        // Calculer la nouvelle position
        $progress = $command->getCurrentIteration() / ($command->getIterations() - 1);

        $newLocation = $this->interpolateLocation(
            $command->getStartLatitude(),
            $command->getStartLongitude(),
            $command->getEndLatitude(),
            $command->getEndLongitude(),
            $progress
        );

        // Créer et sauvegarder la nouvelle location
        $location = new PackageLocation($package, $newLocation['latitude'], $newLocation['longitude']);
        $this->packageLocationRepository->save($location);

        // Dispatcher l'événement de mise à jour de position
        $this->eventBus->dispatch(new PackageLocationUpdatedEvent(
            $package->getId(),
            $newLocation['latitude'],
            $newLocation['longitude'],
            new \DateTimeImmutable()
        ));

        // Planifier la prochaine itération
        $delay = random_int(1000, 3000); // Délai entre 1 et 3 secondes
        $nextCommand = new SimulatePackageLocationCommand(
            $command->getTrackingNumber(),
            $command->getIterations(),
            $command->getStartLatitude(),
            $command->getStartLongitude(),
            $command->getEndLatitude(),
            $command->getEndLongitude(),
            $command->getCurrentIteration() + 1
        );

        $this->commandBus->dispatch($nextCommand, [
            new DelayStamp($delay)
        ]);
    }

    private function calculateProgress(
        int $startTimestamp,
        int $endTimestamp,
        int $currentTimestamp
    ): float {
        $totalDuration = $endTimestamp - $startTimestamp;
        $elapsed = $currentTimestamp - $startTimestamp;
        return min(1, max(0, $elapsed / $totalDuration));
    }

    private function interpolateLocation(
        float $startLat,
        float $startLng,
        float $endLat,
        float $endLng,
        float $progress
    ): array {
        return [
            'latitude' => $startLat + ($endLat - $startLat) * $progress,
            'longitude' => $startLng + ($endLng - $startLng) * $progress
        ];
    }
}
