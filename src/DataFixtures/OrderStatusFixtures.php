<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\PackTrack\Domain\Entity\OrderStatus;

class OrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statuses = [
            'CREATED',
            'WAITING_FOR_PAYMENT',
            'SHIPPED',
            'IN_TRANSIT',
            'DELIVERED',
            'CANCELLED',
        ];

        foreach ($statuses as $status) {
            $orderStatus = new OrderStatus($status);
            $manager->persist($orderStatus);
        }

        $manager->flush();
    }
}
