<?php

namespace App\PackTrack\Infrastructure\Repository;

use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order): void
    {
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?Order
    {
        return $this->find($id);
    }

    /**
     * Récupère les commandes avec leurs packages, paginées.
     *
     * @param int $page
     * @param int $pageSize
     * @return array{orders: Order[], total: int}
     */
    public function findOrdersWithPackagesPaginated(int $page = 1, int $pageSize = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin('o.package', 'p')
            ->addSelect('p')
            ->leftJoin('o.status', 's')
            ->addSelect('s')
            ->orderBy('o.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);

        $orders = $queryBuilder->getQuery()->getResult();

        $totalOrders = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'orders' => $orders,
            'total' => $totalOrders,
        ];
    }

    public function findByReference(string $reference): ?Order
    {
        return $this->findOneBy(['reference' => $reference]);
    }
}
