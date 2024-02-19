<?php

namespace App\Entity;

use App\Repository\HydrometerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: HydrometerRepository::class)]
class Hydrometer
{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'ulid', unique: true, nullable: false)]
        private readonly Ulid $id,

        #[ORM\Column(length: 255, nullable: true)]
        private ?string $name = null,

        #[ORM\Column]
        private readonly \DateTimeImmutable $added = new \DateTimeImmutable(),
    ) {
    }

    public static function create(Ulid $id, ?string $name = null): self
    {
        return new self($id, $name);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAdded(): \DateTimeImmutable
    {
        return $this->added;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
