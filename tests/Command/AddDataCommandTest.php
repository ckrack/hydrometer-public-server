<?php

namespace App\Tests\Command;

use App\Command\AddDataCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

/**
 * @covers \App\Command\AddDataCommand
 *
 * @uses \Symfony\Component\Uid\Ulid
 */
class AddDataCommandTest extends TestCase
{
    private Ulid $ulid;
    private AddDataCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ulid = new Ulid();
        $this->command = new AddDataCommand($this->ulid, '{"foo":"bar"}');
    }

    public function testGetHydrometerId(): void
    {
        $this->assertSame($this->ulid, $this->command->getHydrometerId());
    }

    public function testGetPayload(): void
    {
        $this->assertSame('{"foo":"bar"}', $this->command->getPayload());
    }
}
