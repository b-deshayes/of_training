<?php

namespace App\PackTrack\Domain\Repository;

use App\PackTrack\Domain\Entity\OrderStatus;

interface OrderStatusRepositoryInterface
{
    public function save(OrderStatus $status): void;
    public function findByName(string $name): ?OrderStatus;
    public function findById(int $id): ?OrderStatus;
}
