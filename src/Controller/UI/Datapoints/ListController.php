<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Datapoints;

use App\Entity\DataPoint;
use App\Entity\Hydrometer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ListController extends AbstractController
{
    protected $em;
    protected $logger;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
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

            $data = $this->em->getRepository(DataPoint::class)->findAllByUser($user, $hydrometer);

            // render template
            return $this->render(
                '/ui/datapoints/list.html.twig',
                [
                    'data' => $data,
                    'hydrometer' => $hydrometer,
                ]
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->render(
                'ui/exception.html.twig',
                ['user' => $user]
            );
        }
    }
}
