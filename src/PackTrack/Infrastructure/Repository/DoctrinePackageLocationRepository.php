<?php

namespace App\PackTrack\Infrastructure\Repository; // Namespace mis à jour

use App\PackTrack\Domain\Entity\PackageLocation;
use App\PackTrack\Domain\Repository\PackageLocationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PackageLocation>
 *
 * @method PackageLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackageLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackageLocation[]    findAll()
 * @method PackageLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrinePackageLocationRepository extends ServiceEntityRepository implements PackageLocationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackageLocation::class);
    }

    public function save(PackageLocation $location): void
    {
        $this->getEntityManager()->persist($location);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?PackageLocation
    {
        return $this->find($id);
    }
}
