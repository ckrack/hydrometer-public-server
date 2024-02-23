<?php

namespace App\Handler;

use App\Event\HydrometerDataArchivedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class HydrometerDataArchivedEventHandler
{
    public function __construct(
        private Filesystem $filesystem,
        private ParameterBagInterface $parameterBag,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(HydrometerDataArchivedEvent $hydrometerDataArchivedEvent): void
    {
        try {
            $this->moveData($hydrometerDataArchivedEvent);
        } catch (\Throwable $t) {
            $this->logger->error('Could not handle event', [$t]);
            throw $t;
        }
    }

    private function getFilename(HydrometerDataArchivedEvent $hydrometerDataArchivedEvent): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/public/data/'.$hydrometerDataArchivedEvent->getAggregateId().'.json';
    }

    private function getArchiveFilename(HydrometerDataArchivedEvent $hydrometerDataArchivedEvent): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/public/data/'.$hydrometerDataArchivedEvent->getArchiveId().'.json';
    }

    public function moveData(HydrometerDataArchivedEvent $hydrometerDataArchivedEvent): void
    {
        if ($this->filesystem->exists($this->getFileName($hydrometerDataArchivedEvent))) {
            $this->filesystem->rename($this->getFileName($hydrometerDataArchivedEvent), $this->getArchiveFilename($hydrometerDataArchivedEvent));
            $this->logger->info('Archived data', [$hydrometerDataArchivedEvent->getAggregateId()]);
        }
    }
}
