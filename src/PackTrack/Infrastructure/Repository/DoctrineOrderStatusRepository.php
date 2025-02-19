<?php

namespace App\PackTrack\Infrastructure\Repository;

use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Repository\OrderStatusRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrderStatusRepository extends ServiceEntityRepository implements OrderStatusRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatus::class);
    }

    public function save(OrderStatus $status): void
    {
        $this->getEntityManager()->persist($status);
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?OrderStatus
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findById(int $id): ?OrderStatus
    {
        return $this->find($id);
    }
}
