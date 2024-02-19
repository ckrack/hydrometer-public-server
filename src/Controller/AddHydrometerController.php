<?php

namespace App\Controller;

use App\Command\AddHydrometerCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

class AddHydrometerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/new', name: 'app_new_hydrometer')]
    public function __invoke(): Response
    {
        $hydrometerId = new Ulid();
        $addHydrometerCommand = new AddHydrometerCommand($hydrometerId);
        $this->messageBus->dispatch($addHydrometerCommand);

        return $this->render('hydrometer/new_hydrometer.html.twig', [
            'hydrometer_id' => $hydrometerId,
        ]);
    }
}
