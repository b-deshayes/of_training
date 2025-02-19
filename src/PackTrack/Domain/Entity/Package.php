<?php

namespace App\PackTrack\Domain\Entity;

use App\PackTrack\Infrastructure\Repository\DoctrinePackageRepository;
use App\PackTrack\Domain\Entity\Order;
use App\PackTrack\Domain\Entity\PackageLocation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: DoctrinePackageRepository::class)]
#[ORM\Table(name: "packages")]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read', 'package:read'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['order:read', 'package:read'])]
    private string $trackingNumber;

    #[ORM\OneToOne(inversedBy: 'package', targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\OneToMany(mappedBy: 'package', targetEntity: PackageLocation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['order:read', 'package:read'])]
    private Collection $locations;

    public function __construct(string $trackingNumber, Order $order)
    {
        $this->trackingNumber = $trackingNumber;
        $this->order = $order;
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(string $trackingNumber): self
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
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

    /**
     * @return Collection<int, PackageLocation>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(PackageLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setPackage($this);
        }
        return $this;
    }

    public function removeLocation(PackageLocation $location): self
    {
        if ($this->locations->removeElement($location)) {
            if ($location->getPackage() === $this) {
                $location->setPackage(null);
            }
        }
        return $this;
    }
}
