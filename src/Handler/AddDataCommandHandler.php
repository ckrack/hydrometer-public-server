<?php

namespace App\Handler;

use App\Command\AddDataCommand;
use App\Entity\Hydrometer;
use App\Event\HydrometerDataReceivedEvent;
use App\Repository\HydrometerRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class AddDataCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private HydrometerRepository $hydrometerRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AddDataCommand $addDataCommand): void
    {
        try {
            $hydrometer = $this->hydrometerRepository->find($addDataCommand->getHydrometerId());
            if (!$hydrometer instanceof Hydrometer) {
                throw new \RuntimeException('Hydrometer not found');
            }
            $dataReceivedEvent = HydrometerDataReceivedEvent::create($hydrometer->getId(), $addDataCommand->getPayload());
            $this->messageBus->dispatch($dataReceivedEvent);
        } catch (\Exception $e) {
            $this->logger->error('Could not handle command', [$e]);
        }
    }
}
