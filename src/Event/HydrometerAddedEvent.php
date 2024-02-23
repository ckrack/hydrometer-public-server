<?php

namespace App\Event;

use Symfony\Component\Uid\Ulid;

final readonly class HydrometerAddedEvent implements EventInterface
{
    private function __construct(
        private Ulid $id,
        private Ulid $aggregateId,
        private \DateTimeImmutable $added,
    ) {
    }

    public static function create(
        Ulid $hydrometerId,
        \DateTimeImmutable $added = new \DateTimeImmutable()
    ): self {
        return new self(
            new Ulid(),
            $hydrometerId,
            $added
        );
    }

    public static function getType(): string
    {
        return 'Hydrometer.added';
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [];
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getAggregateId(): Ulid
    {
        return $this->aggregateId;
    }

    public function getAdded(): \DateTimeImmutable
    {
        return $this->added;
    }
}
