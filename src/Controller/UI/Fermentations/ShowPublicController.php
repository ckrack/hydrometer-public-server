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
use App\Security\Voter\PublicFermentationVoter;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ShowPublicController extends AbstractController
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
     * @Route("/fermentations/public/{fermentation}", name="ui_fermentations_show_public")
     * @ParamConverter("fermentation")
     */
    public function __invoke(Fermentation $fermentation)
    {
        $this->denyAccessUnlessGranted(PublicFermentationVoter::VIEW, $fermentation);

        try {
            $latestData = $this->dataPointRepository->findByFermentation($fermentation);
            $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

            $stableSince = $this->statsModule->stableSince($latestData, 'gravity', 0.09);

            // render template
            return $this->render(
                '/ui/fermentations/public.html.twig',
                array_merge(
                    $platoData,
                    [
                        'stable' => $stableSince,
                        'fermentation' => $fermentation,
                    ]
                )
            );
        } catch (Exception $e) {
            return $this->render(
                'ui/exception.html.twig',
                []
            );
        }
    }
}
