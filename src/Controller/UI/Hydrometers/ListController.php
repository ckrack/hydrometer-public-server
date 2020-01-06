<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\DataPoint;
use App\Entity\Hydrometer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends Controller
{
    protected $em;
    protected $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        // add your dependencies
        $this->em = $em;
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
            $hydrometers = $this->em->getRepository(Hydrometer::class)->findAllWithLastActivity($user);

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
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->render(
                'ui/exception.html.twig'
            );
        }
    }

    /**
     * Find the last activity for every hydrometer.
     *
     * @param [type] $hydrometers [description]
     *
     * @return [type] [description]
     */
    protected function findLastActivity(array $hydrometers): array
    {
        foreach ($hydrometers as $key => $hydrometer) {
            if (!empty($hydrometer['last_datapoint_id'])) {
                $activity = $this->em->getRepository(DataPoint::class)->findActivity($hydrometer['last_datapoint_id']);
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
