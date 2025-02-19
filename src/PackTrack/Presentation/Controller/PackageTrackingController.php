<?php

namespace App\PackTrack\Presentation\Controller; // Namespace mis à jour

use App\PackTrack\Domain\Repository\PackageRepositoryInterface; // PackageRepository dans PackTrack\Domain\Repository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\PackTrack\Domain\Entity\Package;
use App\PackTrack\Domain\Repository\PackageLocationRepositoryInterface;
use App\PackTrack\Domain\Entity\PackageLocation;
use Symfony\Component\Messenger\MessageBusInterface;
use App\PackTrack\Application\DTO\SimulatePackageLocationDTO;
use App\PackTrack\Application\Command\SimulatePackageLocationCommand;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api/packages', name: 'api_packages_')]
class PackageTrackingController extends AbstractController
{
    public function __construct(
        private PackageRepositoryInterface $packageRepository,
        private PackageLocationRepositoryInterface $packageLocationRepository,
        private MessageBusInterface $commandBus
    ) {
    }

    #[Route('/{trackingNumber}/simulate', name: 'simulate_package_location', methods: ['PATCH'])]
    public function simulatePackageLocation(
        string $trackingNumber,
        #[MapRequestPayload] SimulatePackageLocationDTO $dto
    ): JsonResponse
    {
        $command = new SimulatePackageLocationCommand(
            $trackingNumber,
            $dto->getIterations(),
            $dto->getStartLatitude(),
            $dto->getStartLongitude(),
            $dto->getEndLatitude(),
            $dto->getEndLongitude()
        );

        $this->commandBus->dispatch($command);

        return $this->json(['status' => 'simulation_started'], Response::HTTP_ACCEPTED);
    }
}
