<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\DataPoint;
use App\Entity\Fermentation;
use App\Modules\Stats;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ShowPublicController extends Controller
{
    protected $em;
    protected $statsModule;

    public function __construct(
        EntityManagerInterface $em,
        Stats\Data $statsModule
    ) {
        // add your dependencies
        $this->em = $em;
        $this->statsModule = $statsModule;
    }

    /**
     * @Route("/fermentations/public/{fermentation}", name="ui_fermentations_show_public")
     * @ParamConverter("fermentation")
     */
    public function __invoke(Fermentation $fermentation)
    {
        // check for "view" access: calls all voters
        $this->denyAccessUnlessGranted('view', $fermentation);

        try {
            if (!$fermentation->isPublic()) {
                throw new \Exception('Fermentation is not public');
            }

            $latestData = $this->em->getRepository(DataPoint::class)->findByFermentation($fermentation);
            $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

            $stableSince = $this->statsModule->stableSince($latestData, 'gravity', 0.09);

            // render template
            return $this->render(
                '/ui/fermentations/public.php',
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
