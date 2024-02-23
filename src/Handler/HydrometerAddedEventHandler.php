<?php

namespace App\Handler;

use App\Entity\Hydrometer;
use App\Event\HydrometerAddedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class HydrometerAddedEventHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(HydrometerAddedEvent $hydrometerAddedEvent): void
    {
        $hydrometer = Hydrometer::create($hydrometerAddedEvent->getAggregateId());
        $this->entityManager->persist($hydrometer);
        $this->entityManager->flush();
        $this->logger->info('Added hydrometer', [$hydrometerAddedEvent]);
    }
}
