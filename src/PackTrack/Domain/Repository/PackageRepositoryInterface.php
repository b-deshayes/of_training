<?php

namespace App\PackTrack\Domain\Repository; // Namespace mis à jour

use App\PackTrack\Domain\Entity\Package;

interface PackageRepositoryInterface
{
    public function save(Package $package): void;
    public function findByTrackingNumber(string $trackingNumber): ?Package;
    public function findById(int $id): ?Package;
    // autres méthodes si nécessaire
}
