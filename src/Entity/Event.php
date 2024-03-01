<?php

namespace App\Entity;

use App\Event\EventInterface;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    private function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'ulid', unique: true, nullable: false)]
        private readonly Ulid $id,

        #[ORM\Column(type: 'ulid', nullable: true)]
        private readonly ?Ulid $aggregateId,

        #[ORM\Column]
        private readonly \DateTimeImmutable $added,

        #[ORM\Column(length: 255)]
        private readonly string $type,

        /**
         * @var array<array-key, mixed>
         */
        #[ORM\Column]
        private readonly array $data,
    ) {
    }

    public static function fromEvent(EventInterface $event): Event
    {
        return new Event(
            $event->getId(),
            $event->getAggregateId(),
            $event->getAdded(),
            $event::getType(),
            $event->jsonSerialize()
        );
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getAggregateId(): ?Ulid
    {
        return $this->aggregateId;
    }

    public function getAdded(): \DateTimeImmutable
    {
        return $this->added;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }
}
