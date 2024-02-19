<?php

namespace App\Event;

use Symfony\Component\Uid\Ulid;

interface EventInterface extends \JsonSerializable
{
    public static function getType(): string;

    public function getId(): Ulid;

    public function getAggregateId(): ?Ulid;

    public function getAdded(): \DateTimeImmutable;

    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array;
}
