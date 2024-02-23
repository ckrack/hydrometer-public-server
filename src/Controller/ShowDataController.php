<?php

namespace App\Controller;

use App\Repository\HydrometerRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

class ShowDataController extends AbstractController
{
    public function __construct(
        private readonly HydrometerRepository $hydrometerRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/show/{hydrometerId}', name: 'app_show_data')]
    public function __invoke(Ulid $hydrometerId): Response
    {
        try {
            $hydrometer = $this->hydrometerRepository->find($hydrometerId);

            return $this->render('hydrometer/show_data.html.twig', [
                'hydrometer' => $hydrometer,
                'hydrometer_id' => $hydrometerId,
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
