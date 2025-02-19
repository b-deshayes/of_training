<?php

namespace Tests\PackTrack\Domain\Entity;

use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Entity\OrderStatusHistory;
use App\PackTrack\Domain\Entity\Package;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testOrderCreation(): void
    {
        $order = new Order('REF-123');
        $this->assertEquals('REF-123', $order->getReference());
        $this->assertInstanceOf(\DateTimeImmutable::class, $order->getCreatedAt());
        $this->assertEmpty($order->getPackages());
        $this->assertEmpty($order->getStatusHistory());
    }

    public function testSetAndGetReference(): void
    {
        $order = new Order('REF-INITIAL');
        $order->setReference('REF-MODIFIED');
        $this->assertEquals('REF-MODIFIED', $order->getReference());
    }

    public function testSetAndGetStatus(): void
    {
        $order = new Order('REF-123');
        $status = new OrderStatus('Pending');
        $order->setStatus($status);
        $this->assertSame($status, $order->getStatus());
        $this->assertInstanceOf(\DateTimeImmutable::class, $order->getUpdatedAt());
    }

    public function testAddAndRemovePackage(): void
    {
        $order = new Order('REF-123');
        $package1 = new Package('TRACK-1', $order);
        $package2 = new Package('TRACK-2', $order);

        $order->addPackage($package1);
        $order->addPackage($package2);

        $this->assertCount(2, $order->getPackages());
        $this->assertTrue($order->getPackages()->contains($package1));
        $this->assertTrue($order->getPackages()->contains($package2));
        $this->assertSame($order, $package1->getOrder());
        $this->assertSame($order, $package2->getOrder());

        $order->removePackage($package1);
        $this->assertCount(1, $order->getPackages());
        $this->assertFalse($order->getPackages()->contains($package1));
        $this->assertTrue($order->getPackages()->contains($package2));
        $this->assertNull($package1->getOrder()); // Bidirectional relationship handled
    }

    public function testAddAndRemoveStatusHistory(): void
    {
        $order = new Order('REF-123');
        $status1 = new OrderStatus('Created');
        $status2 = new OrderStatus('Shipped');
        $statusHistory1 = new OrderStatusHistory($order, $status1);
        $statusHistory2 = new OrderStatusHistory($order, $status2);

        $order->addStatusHistory($statusHistory1);
        $order->addStatusHistory($statusHistory2);

        $this->assertCount(2, $order->getStatusHistory());
        $this->assertTrue($order->getStatusHistory()->contains($statusHistory1));
        $this->assertTrue($order->getStatusHistory()->contains($statusHistory2));
        $this->assertSame($order, $statusHistory1->getOrder());
        $this->assertSame($order, $statusHistory2->getOrder());

        $order->removeStatusHistory($statusHistory1);
        $this->assertCount(1, $order->getStatusHistory());
        $this->assertFalse($order->getStatusHistory()->contains($statusHistory1));
        $this->assertTrue($order->getStatusHistory()->contains($statusHistory2));
        $this->assertNull($statusHistory1->getOrder()); // Bidirectional relationship handled
    }
}
