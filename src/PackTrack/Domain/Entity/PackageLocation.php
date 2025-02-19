<?php

namespace App\PackTrack\Domain\Entity;

use App\PackTrack\Infrastructure\Repository\DoctrinePackageLocationRepository;
use App\PackTrack\Domain\Entity\Package;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: DoctrinePackageLocationRepository::class)]
#[ORM\Table(name: "package_locations")]
class PackageLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'float')]
    #[Groups(['order:read', 'package:read'])]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    #[Groups(['order:read', 'package:read'])]
    private float $longitude;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['order:read', 'package:read'])]
    private \DateTimeImmutable $timestamp;

    #[ORM\ManyToOne(targetEntity: Package::class, inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private Package $package;

    public function __construct(Package $package, float $latitude, float $longitude)
    {
        $this->package = $package;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->timestamp = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function setPackage(Package $package): self
    {
        $this->package = $package;
        return $this;
    }
}
