<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Repository\DataPointRepository;
use App\Repository\HydrometerRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ListController extends AbstractController
{
    private $hydrometerRepository;
    private $dataPointRepository;
    private $logger;

    public function __construct(HydrometerRepository $hydrometerRepository, DataPointRepository $dataPointRepository, LoggerInterface $logger)
    {
        $this->hydrometerRepository = $hydrometerRepository;
        $this->dataPointRepository = $dataPointRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/ui/hydrometers", name="ui_hydrometers_list")
     * @Route("/ui")
     * @Route("/ui/")
     */
    public function __invoke()
    {
        try {
            $user = $this->getUser();
            $hydrometers = $this->hydrometerRepository->findAllWithLastActivity($user);

            $hydrometers = $this->findLastActivity($hydrometers);

            // render template
            return $this->render(
                '/ui/hydrometers/list.html.twig',
                [
                    'hydrometers' => $hydrometers,
                    'user' => $user,
                    'form' => $this->createDeleteForm()->createView(),
                ]
            );
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return $this->render(
                'ui/exception.html.twig'
            );
        }
    }

    /**
     * Find the last activity for every hydrometer.
     */
    private function findLastActivity(array $hydrometers): array
    {
        foreach ($hydrometers as $key => $hydrometer) {
            if (!array_key_exists('last_datapoint_id', $hydrometer) || null === $hydrometer['last_datapoint_id']) {
                $activity = $this->dataPointRepository->findActivity($hydrometer['last_datapoint_id']);
                $hydrometers[$key] = array_merge($hydrometer, (array) $activity);
            }
        }

        return $hydrometers;
    }

    /**
     * Creates a form to delete a fermentation.
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
