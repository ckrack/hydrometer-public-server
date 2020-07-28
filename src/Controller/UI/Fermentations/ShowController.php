<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\Fermentation;
use App\Modules\Stats;
use App\Repository\DataPointRepository;
use App\Security\Voter\OwnerVoter;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ShowController extends AbstractController
{
    private $dataPointRepository;
    private $statsModule;

    public function __construct(
        DataPointRepository $dataPointRepository,
        Stats\Data $statsModule
    ) {
        // add your dependencies
        $this->dataPointRepository = $dataPointRepository;
        $this->statsModule = $statsModule;
    }

    /**
     * @Route("/ui/fermentations/{fermentation}", name="ui_fermentations_show")
     * @ParamConverter("fermentation")
     */
    public function __invoke(Fermentation $fermentation)
    {
        $this->denyAccessUnlessGranted(OwnerVoter::VIEW, $fermentation);

        try {
            $user = $this->getUser();

            if ($fermentation->getUser()->getId() !== $user->getId()) {
                throw new Exception('No access');
            }

            $latestData = $this->dataPointRepository->findByFermentation($fermentation);

            $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

            $stableSince = $this->statsModule->stableSince($latestData, 'gravity', 0.09);

            // render template
            return $this->render(
                '/ui/fermentations/details.html.twig',
                array_merge(
                    $platoData,
                    [
                        'user' => $user,
                        'stable' => $stableSince,
                        'fermentation' => $fermentation,
                    ]
                )
            );
        } catch (Exception $exception) {
            return $this->render(
                'ui/exception.html.twig',
                ['user' => $user]
            );
        }
    }
}
