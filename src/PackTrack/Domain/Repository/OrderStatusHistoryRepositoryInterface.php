<?php

namespace App\PackTrack\Domain\Repository;

use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatusHistory;

interface OrderStatusHistoryRepositoryInterface
{
    public function save(OrderStatusHistory $statusHistory): void;
    public function findByOrder(Order $order): array;
    public function findById(int $id): ?OrderStatusHistory;
}
