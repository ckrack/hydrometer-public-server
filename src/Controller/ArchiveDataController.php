<?php

namespace App\Controller;

use App\Event\HydrometerDataArchivedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

class ArchiveDataController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route('/archive/{hydrometerId}', name: 'app_archive_data')]
    public function __invoke(Ulid $hydrometerId, Request $request): Response
    {
        try {
            $archiveId = new Ulid();
            $hydrometerArchivedEvent = HydrometerDataArchivedEvent::create($hydrometerId, $archiveId);
            $this->messageBus->dispatch($hydrometerArchivedEvent);

            return $this->render('hydrometer/archive_data.html.twig', [
                'archive_id' => $archiveId,
                'hydrometer_id' => $hydrometerId,
            ]);
        } catch (\Exception) {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
