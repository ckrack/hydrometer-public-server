<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Tests;

use App\Entity\DataPoint;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

class ISpindelTCPApiTest extends KernelTestCase
{
    use FixturesTrait;

    private $fixtures;
    private $entityManager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->fixtures = $this->loadFixtures([
            'App\DataFixtures\AppFixtures',
        ])->getReferenceRepository();

        self::ensureKernelShutdown();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testISpindelTCPAPI()
    {
        $process = new Process(['php', 'bin/console', 'app:ispindel-tcp-server', '--port', '9001']);
        $process->start();

        // send data via a TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // wait for socket to be open
        sleep(2);

        $this->assertTrue(socket_connect($socket, 'localhost', '9001'));

        $in = "{\r\n";
        $in .= '"name":"test-hydrometer","ID":"123456789","angle":29.01,"temperature":5.24,"battery":5.5,"gravity":1.31, "token": "'.$this->fixtures->getReference('test-token')->getValue().'"}'."\r\n";
        $in .= "\r\n";
        $in .= "\r\n";
        $out = '';

        socket_write($socket, $in, strlen($in));

        // @TODO implement setting new interval?
        //$out .= socket_read($socket, 2048);

        socket_close($socket);

        sleep(1);

        // close server process
        $process->stop(1, 3);

        // the output of the command in the console
        $output = $process->getOutput();
        $this->assertStringContainsString('Socket server open', $output);

        $datapoint = $this->entityManager
            ->getRepository(DataPoint::class)
            ->findOneBy([
                'hydrometer' => $this->fixtures->getReference('test-hydrometer'),
            ])
        ;

        $this->assertSame(29.01, $datapoint->getAngle());
        $this->assertSame(5.24, $datapoint->getTemperature());
        $this->assertSame(1.31, $datapoint->getGravity());
    }
}
