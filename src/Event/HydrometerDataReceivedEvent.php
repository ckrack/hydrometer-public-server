<?php

namespace App\Event;

use Symfony\Component\Uid\Ulid;

readonly class HydrometerDataReceivedEvent implements EventInterface
{
    private function __construct(
        private Ulid $id,
        private Ulid $aggregateId,
        private \DateTimeImmutable $added,
        private string $payload,
    ) {
    }

    public static function create(Ulid $hydrometerId, string $payload): self
    {
        return new self(
            new Ulid(),
            $hydrometerId,
            new \DateTimeImmutable(),
            $payload
        );
    }

    public static function getType(): string
    {
        return 'Hydrometer.Data.received';
    }

    /**
     * @return array{payload: string}
     */
    public function jsonSerialize(): array
    {
        return ['payload' => $this->payload];
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
