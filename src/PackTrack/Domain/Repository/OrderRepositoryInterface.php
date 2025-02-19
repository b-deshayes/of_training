<?php

declare(strict_types=1);

namespace App\PackTrack\Domain\Repository;

use App\PackTrack\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(int $id): ?Order;
    public function findByReference(string $reference): ?Order;
    public function findOrdersWithPackagesPaginated(int $page = 1, int $pageSize = 10): array;
}
