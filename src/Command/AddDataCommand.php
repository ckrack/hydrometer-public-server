<?php

namespace App\Command;

use Symfony\Component\Uid\Ulid;

final readonly class AddDataCommand
{
    public function __construct(private Ulid $hydrometerId, private string $payload)
    {
    }

    public function getHydrometerId(): Ulid
    {
        return $this->hydrometerId;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }
}
