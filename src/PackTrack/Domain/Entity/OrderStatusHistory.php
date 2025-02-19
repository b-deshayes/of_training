<?php

namespace App\PackTrack\Domain\Entity;

use App\PackTrack\Infrastructure\Repository\DoctrineOrderStatusHistoryRepository;
use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\OrderStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: DoctrineOrderStatusHistoryRepository::class)]
#[ORM\Table(name: "order_status_histories")]
class OrderStatusHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'statusHistory')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: OrderStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:read'])]
    private OrderStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['order:read'])]
    private \DateTimeImmutable $changeDate;

    public function __construct(Order $order, OrderStatus $status)
    {
        $this->order = $order;
        $this->status = $status;
        $this->changeDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getChangeDate(): \DateTimeImmutable
    {
        return $this->changeDate;
    }

    public function setChangeDate(\DateTimeImmutable $changeDate): self
    {
        $this->changeDate = $changeDate;
        return $this;
    }
}
