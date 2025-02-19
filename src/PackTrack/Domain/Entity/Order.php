<?php

namespace App\PackTrack\Domain\Entity;

use App\PackTrack\Infrastructure\Repository\DoctrineOrderRepository;
use App\PackTrack\Domain\Entity\OrderStatus;
use App\PackTrack\Domain\Entity\OrderStatusHistory;
use App\PackTrack\Domain\Entity\Package;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DoctrineOrderRepository::class)]
#[ORM\Table(name: "orders")]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['order:read'])]
    private string $reference;

    #[ORM\ManyToOne(targetEntity: OrderStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:read'])]
    private OrderStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['order:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['order:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderStatusHistory::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $statusHistory;

    #[ORM\OneToOne(mappedBy: 'order', targetEntity: Package::class, cascade: ['persist', 'remove'], orphanRemoval: true, fetch: 'EAGER')]
    #[Groups(['order:read'])]
    private ?Package $package = null;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
        $this->createdAt = new \DateTimeImmutable();
        $this->statusHistory = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, OrderStatusHistory>
     */
    public function getStatusHistory(): Collection
    {
        return $this->statusHistory;
    }

    public function addStatusHistory(OrderStatusHistory $statusHistory): self
    {
        if (!$this->statusHistory->contains($statusHistory)) {
            $this->statusHistory[] = $statusHistory;
            $statusHistory->setOrder($this);
        }
        return $this;
    }

    public function removeStatusHistory(OrderStatusHistory $statusHistory): self
    {
        if ($this->statusHistory->removeElement($statusHistory)) {
            // set the owning side to null (unless already changed)
            if ($statusHistory->getOrder() === $this) {
                $statusHistory->setOrder(null);
            }
        }
        return $this;
    }

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): self
    {
        if ($package === null && $this->package !== null) {
            $this->package->setOrder(null);
        }

        if ($package !== null && $package->getOrder() !== $this) {
            $package->setOrder($this);
        }

        $this->package = $package;
        return $this;
    }
}
