<?php

namespace App\Tests;

use App\Entity\Hydrometer;
use App\Repository\HydrometerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Uid\Ulid;

class BrewingFlowFunctionalTest extends WebTestCase
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

    protected function getService(string $id): mixed
    {
        return self::getContainer()
            ->get($id);
    }

    public function testCanAddHydrometer(): Hydrometer
    {
        self::$client->request('POST', '/new');
        $this->assertResponseRedirects();
        $crawler = self::$client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'New hydrometer added ✅');
        $this->assertSelectorTextContains('p.intro', 'Your new hydrometer token is: ');

        $hydrometerId = Ulid::fromString($crawler->filter('code.ulid')->text());

        $hydrometerRepository = $this->getService(HydrometerRepository::class);
        $hydrometer = $hydrometerRepository->find($hydrometerId);
        $this->assertInstanceOf(Hydrometer::class, $hydrometer);
        $parameterBag = $this->getService(ParameterBagInterface::class);
        $dataFilename = $parameterBag->get('kernel.project_dir').'/public/data/'.$hydrometer->getId().'.json';
        $this->assertFileExists($dataFilename);
        $this->assertStringEqualsFile($dataFilename, '[]');

        return $hydrometer;
    }

    /**
     * @depends testCanAddHydrometer
     */
    public function testShowsEmptyData(Hydrometer $hydrometer): Hydrometer
    {
        $crawler = self::$client->request('GET', '/show/'.$hydrometer->getId());
        $this->assertSelectorTextContains('h1', 'Data for '.$hydrometer->getId());

        return $hydrometer;
    }

    /**
     * @depends testShowsEmptyData
     */
    public function testCanPostData(Hydrometer $hydrometer): Hydrometer
    {
        $data = [
                    'name' => 'Hydro-Test',
                    'ID' => '123456',
                    'angle' => 78.9,
                    'temperature' => 10.11,
                    'battery' => 5.43,
                    'gravity' => 12.34,
                    'token' => $hydrometer->getId(),
        ];
        self::$client->request(
            'POST',
            '/data/'.$hydrometer->getId(),
            content: json_encode($data, JSON_THROW_ON_ERROR)
        );
        $this->assertResponseIsSuccessful();
        $parameterBag = $this->getService(ParameterBagInterface::class);
        $dataFilename = $parameterBag->get('kernel.project_dir').'/public/data/'.$hydrometer->getId().'.json';
        $jsonFromDataFile = (string) file_get_contents($dataFilename);
        $savedData = json_decode($jsonFromDataFile, true, 512, JSON_THROW_ON_ERROR);
        unset($savedData[0]['time']);
        $this->assertEquals($data, $savedData[0]);

        return $hydrometer;
    }
}
