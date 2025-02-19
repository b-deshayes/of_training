<?php

namespace App\PackTrack\Infrastructure\Repository; // Namespace mis à jour

use App\PackTrack\Domain\Entity\Package;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 *
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrinePackageRepository extends ServiceEntityRepository implements PackageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function save(Package $package): void
    {
        $this->getEntityManager()->persist($package);
        $this->getEntityManager()->flush();
    }

    public function findByTrackingNumber(string $trackingNumber): ?Package
    {
        return $this->findOneBy(['trackingNumber' => $trackingNumber]);
    }

    public function findById(int $id): ?Package
    {
        return $this->find($id);
    }
}
