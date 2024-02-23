<?php

namespace App\Command;

use Symfony\Component\Uid\Ulid;

final readonly class AddHydrometerCommand
{
    public function __construct(private Ulid $id)
    {
    }

    public function getId(): Ulid
    {
        return $this->id;
    }
}
