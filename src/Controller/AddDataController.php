<?php

namespace App\Controller;

use App\Command\AddDataCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

class AddDataController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route('/data/{hydrometerId}', name: 'app_add_data', methods: [Request::METHOD_POST])]
    public function __invoke(Ulid $hydrometerId, Request $request): Response
    {
        try {
            $addDataCommand = new AddDataCommand($hydrometerId, $request->getContent());
            $response = new Response('', Response::HTTP_OK);
            $response->send();
            $this->messageBus->dispatch($addDataCommand);

            return $response;
        } catch (\Exception) {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
