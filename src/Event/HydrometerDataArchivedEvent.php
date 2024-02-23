<?php

namespace App\Event;

use Symfony\Component\Uid\Ulid;

final readonly class HydrometerDataArchivedEvent implements EventInterface
{
    private function __construct(
        private Ulid $id,
        private Ulid $aggregateId,
        private Ulid $archiveId,
        private \DateTimeImmutable $added,
    ) {
    }

    public static function create(Ulid $hydrometerId, Ulid $archiveId): HydrometerDataArchivedEvent
    {
        return new HydrometerDataArchivedEvent(
            new Ulid(),
            $hydrometerId,
            $archiveId,
            new \DateTimeImmutable(),
        );
    }

    public static function getType(): string
    {
        return 'Hydrometer.Data.archived';
    }

    /**
     * @return array{archive_id: Ulid}
     */
    public function jsonSerialize(): array
    {
        return ['archive_id' => $this->archiveId];
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

    public function getArchiveId(): Ulid
    {
        return $this->archiveId;
    }
}
