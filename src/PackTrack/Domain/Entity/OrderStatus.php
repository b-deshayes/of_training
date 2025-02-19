<?php

namespace App\PackTrack\Domain\Entity;

use App\PackTrack\Infrastructure\Repository\DoctrineOrderStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DoctrineOrderStatusRepository::class)]
#[ORM\Table(name: "order_statuses")]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['order:read'])]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
