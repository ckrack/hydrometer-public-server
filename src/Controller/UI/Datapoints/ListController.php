<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Datapoints;

use App\Entity\Hydrometer;
use App\Repository\DataPointRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ListController extends AbstractController
{
    private $dataPointRepository;
    private $logger;

    public function __construct(
        DataPointRepository $dataPointRepository,
        LoggerInterface $logger
    ) {
        $this->dataPointRepository = $dataPointRepository;
        $this->logger = $logger;
    }

    /**
     * List of datapoints.
     *
     * @Route("/ui/datapoints", name="ui_datapoints_list")
     * @Route("/ui/datapoints/{hydrometer}", name="ui_datapoints_list_hydrometer")
     * @ParamConverter("hydrometer")
     */
    public function __invoke(Hydrometer $hydrometer = null)
    {
        try {
            $user = $this->getUser();

            if ($hydrometer && $hydrometer->getUser()->getId() !== $user->getId()) {
                throw new Exception('No access');
            }

            $data = $this->dataPointRepository->findAllByUser($user, $hydrometer);

            // render template
            return $this->render(
                '/ui/datapoints/list.html.twig',
                [
                    'data' => $data,
                    'hydrometer' => $hydrometer,
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            return $this->render(
                'ui/exception.html.twig',
                ['user' => isset($user) ? $user : null]
            );
        }
    }
}
