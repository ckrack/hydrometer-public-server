<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
        self::bootKernel();
    }

    public static function tearDownAfterClass(): void
    {
        self::ensureKernelShutdown();
    }

    protected function tearDown(): void
    {
    }

    public function testShowsButtonToAddNewHydrometer(): void
    {
        self::$client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello Homebrewer!');
        $this->assertSelectorTextContains('button', 'Add new hydrometer');
    }
}
