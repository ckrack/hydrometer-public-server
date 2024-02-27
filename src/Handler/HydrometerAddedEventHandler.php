<?php

namespace App\Handler;

use App\Entity\Hydrometer;
use App\Event\HydrometerAddedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class HydrometerAddedEventHandler
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $entityManager,
        private Filesystem $filesystem,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(HydrometerAddedEvent $hydrometerAddedEvent): void
    {
        $hydrometer = $this->addHydrometerToRepository($hydrometerAddedEvent);
        $this->createEmptyDataFile($hydrometer);
        $this->logger->info('Added hydrometer', [$hydrometerAddedEvent]);
    }

    public function createEmptyDataFile(Hydrometer $hydrometer): void
    {
        $this->filesystem->dumpFile(
            Hydrometer::getFilenameForIdFromParameterBag($this->parameterBag, $hydrometer->getId()),
            '[]'
        );
    }

    public function addHydrometerToRepository(HydrometerAddedEvent $hydrometerAddedEvent): Hydrometer
    {
        $hydrometer = Hydrometer::create($hydrometerAddedEvent->getAggregateId());
        $this->entityManager->persist($hydrometer);
        $this->entityManager->flush();

        return $hydrometer;
    }
}
