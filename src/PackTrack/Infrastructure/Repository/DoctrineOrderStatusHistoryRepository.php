<?php

namespace App\PackTrack\Infrastructure\Repository;

use App\PackTrack\Domain\Entity\OrderStatusHistory;
use App\PackTrack\Domain\Repository\OrderStatusHistoryRepositoryInterface;
use App\PackTrack\Domain\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrderStatusHistoryRepository extends ServiceEntityRepository implements OrderStatusHistoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderStatusHistory::class);
    }

    public function save(OrderStatusHistory $statusHistory): void
    {
        $this->getEntityManager()->persist($statusHistory);
        $this->getEntityManager()->flush();
    }

    public function findByOrder(Order $order): array
    {
        return $this->findBy(['order' => $order], ['changeDate' => 'ASC']);
    }

    public function findById(int $id): ?OrderStatusHistory
    {
        return $this->find($id);
    }
}
