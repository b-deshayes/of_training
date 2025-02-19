<?php

namespace App\PackTrack\Domain\Repository; // Namespace mis à jour

use App\PackTrack\Domain\Entity\PackageLocation;

interface PackageLocationRepositoryInterface
{
    public function save(PackageLocation $location): void;
    public function findById(int $id): ?PackageLocation;
    // autres méthodes si nécessaire
}
