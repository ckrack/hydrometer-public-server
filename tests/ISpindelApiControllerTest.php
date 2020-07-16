<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Tests;

use App\Entity\DataPoint;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ISpindelApiControllerTest extends WebTestCase
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

    public function testISpindelHTTPAPI()
    {
        $client = static::createClient();

        // send raw post request
        $crawler = $client->request(
            'POST',
            '/api/ispindel/'.$this->fixtures->getReference('test-token')->getValue(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"name":"test-hydrometer","ID":"123456789","angle":31.1,"temperature":24.05,"battery":5.5,"gravity":1.29, "token": "'.$this->fixtures->getReference('test-token')->getValue().'", "temp_units":"C","interval":900,"RSSI":-64}'
        );

        $this->assertResponseIsSuccessful();

        $datapoint = $this->entityManager
            ->getRepository(DataPoint::class)
            ->findOneBy([
                'hydrometer' => $this->fixtures->getReference('test-hydrometer'),
            ])
        ;

        $this->assertSame(31.1, $datapoint->getAngle());
        $this->assertSame(24.05, $datapoint->getTemperature());
        $this->assertSame(1.29, $datapoint->getGravity());
    }
}
