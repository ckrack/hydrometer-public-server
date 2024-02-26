<?php

namespace App\Tests\Command;

use App\Command\AddHydrometerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

/**
 * @covers \App\Command\AddHydrometerCommand
 *
 * @uses \Symfony\Component\Uid\Ulid
 */
class AddHydrometerCommandTest extends TestCase
{
    public function testGetId(): void
    {
        $ulid = new Ulid();
        $command = new AddHydrometerCommand($ulid);
        $this->assertSame($ulid, $command->getId());
    }
}
