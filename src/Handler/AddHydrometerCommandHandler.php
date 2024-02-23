<?php

namespace App\Handler;

use App\Command\AddHydrometerCommand;
use App\Event\HydrometerAddedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class AddHydrometerCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(AddHydrometerCommand $addHydrometerCommand): void
    {
        $hydrometerAddedEvent = HydrometerAddedEvent::create($addHydrometerCommand->getId());
        $this->messageBus->dispatch($hydrometerAddedEvent);
    }
}
