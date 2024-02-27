<?php

namespace App\Handler;

use App\Entity\Hydrometer;
use App\Event\HydrometerDataReceivedEvent;
use App\Repository\HydrometerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class HydrometerDataReceivedEventHandler
{
    public function __construct(
        private HydrometerRepository $hydrometerRepository,
        private EntityManagerInterface $entityManager,
        private Filesystem $filesystem,
        private ParameterBagInterface $parameterBag,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): void
    {
        try {
            $this->saveData($hydrometerDataReceivedEvent);
            $this->updateName($hydrometerDataReceivedEvent);
        } catch (\JsonException $e) {
            $this->logger->error('Could not decode json', [$e]);
        } catch (\Throwable $t) {
            $this->logger->error('Could not handle event', [$t]);
            throw $t;
        }
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \JsonException
     */
    private function compileJsonData(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): array
    {
        $payload = $hydrometerDataReceivedEvent->jsonSerialize()['payload'];
        $payloadArray = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        $payloadArray['time'] = $hydrometerDataReceivedEvent->getAdded()->format('Y-m-d H:i:s');

        return $payloadArray;
    }

    private function getFileName(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): string
    {
        return Hydrometer::getFilenameForIdFromParameterBag($this->parameterBag, $hydrometerDataReceivedEvent->getAggregateId());
    }

    /**
     * @throws \JsonException
     */
    public function saveData(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): void
    {
        $data = $this->getExistingJsonData($hydrometerDataReceivedEvent);
        $data[] = $this->compileJsonData($hydrometerDataReceivedEvent);
        $this->filesystem->dumpFile($this->getFileName($hydrometerDataReceivedEvent), $this->getEncodedData($data));
        $this->logger->debug('Added data', [$data, $hydrometerDataReceivedEvent->getAggregateId()]);
    }

    /**
     * @throws \JsonException
     */
    private function updateName(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): void
    {
        $data = $this->compileJsonData($hydrometerDataReceivedEvent);
        if (!array_key_exists('name', $data)) {
            return;
        }

        $hydrometer = $this->hydrometerRepository->find($hydrometerDataReceivedEvent->getAggregateId());

        if (!$hydrometer instanceof Hydrometer) {
            return;
        }

        $hydrometer->setName($data['name']);
        $this->entityManager->flush();
    }

    /**
     * @return array|mixed
     *
     * @throws \JsonException
     */
    public function getExistingJsonData(HydrometerDataReceivedEvent $hydrometerDataReceivedEvent): mixed
    {
        try {
            if (!$this->filesystem->exists($this->getFileName($hydrometerDataReceivedEvent))) {
                return [];
            }
            $json = file_get_contents($this->getFileName($hydrometerDataReceivedEvent));
            if (!is_string($json)) {
                return [];
            }

            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }
    }

    public function getEncodedData(mixed $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        if (is_string($json)) {
            return $json;
        }

        return '[]';
    }
}
