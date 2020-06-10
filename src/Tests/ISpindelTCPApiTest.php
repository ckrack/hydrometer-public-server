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

        $this->process = new Process(['php', 'bin/console', 'app:ispindel-tcp-server', '--port', '9001']);
        $this->process->start();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // close server process
        $this->process->stop(1, 3);

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testISpindelTCPAPI()
    {
        // send data via a TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // wait for socket to be open
        sleep(2);

        $this->assertTrue(socket_connect($socket, 'localhost', '9001'));

        $in = "{\r\n";
        $in .= '"name":"test-hydrometer","ID":"123456789","angle":29.01,"temperature":5.24,"battery":5.5,"gravity":1.31,"interval": 1200,"token": "'.$this->fixtures->getReference('test-token')->getValue().'"}'."\r\n";
        $in .= "\r\n";
        $out = '';

        socket_write($socket, $in, mb_strlen($in));

        socket_recv($socket, $out, 2048, MSG_WAITALL);
        socket_close($socket);

        // hydrometer has no interval set, expect the one that was sent in the request.
        $this->assertJsonStringEqualsJsonString(
            $out,
            json_encode((object) ['interval' => 1200])
        );

        // wait until server saved in DB
        sleep(2);

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

    public function testISpindelTCPAPIInterval()
    {
        // send data via a TCP/IP socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        // wait for socket to be open
        sleep(2);

        $this->assertTrue(socket_connect($socket, 'localhost', '9001'));

        $in = "{\r\n";
        $in .= '"name":"test-hydrometer-interval","ID":"123456789","angle":29.01,"temperature":5.24,"battery":5.5,"gravity":1.31,"interval": 1200,"token": "'.$this->fixtures->getReference('test-token-interval')->getValue().'"}'."\r\n";
        $in .= "\r\n";
        $out = '';

        socket_write($socket, $in, mb_strlen($in));
        socket_recv($socket, $out, 2048, MSG_WAITALL);
        socket_close($socket);

        // setting new interval by the one that is specified in the server side hydrometer settings
        $this->assertJsonStringEqualsJsonString(
            $out,
            json_encode((object) ['interval' => 600])
        );

        // wait until server saved in DB
        sleep(2);

        $datapoint = $this->entityManager
            ->getRepository(DataPoint::class)
            ->findOneBy([
                'hydrometer' => $this->fixtures->getReference('test-hydrometer-interval'),
            ])
        ;

        $this->assertSame(29.01, $datapoint->getAngle());
        $this->assertSame(5.24, $datapoint->getTemperature());
        $this->assertSame(1.31, $datapoint->getGravity());
    }
}
