<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller;

use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\Token;
use App\Form\ThrowawayFermentationType;
use App\Modules\Stats;
use App\Repository\DataPointRepository;
use App\Repository\FermentationRepository;
use App\Repository\HydrometerRepository;
use App\Repository\TokenRepository;
use App\Security\Voter\PublicFermentationVoter;
use Exception;
use Jenssegers\Optimus\Optimus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ThrowAwayFermentationController extends AbstractController
{
    private $dataPointRepository;
    private $fermentationRepository;
    private $hydrometerRepository;
    private $tokenRepository;
    private $statsModule;
    private $optimus;

    public function __construct(
        DataPointRepository $dataPointRepository,
        FermentationRepository $fermentationRepository,
        HydrometerRepository $hydrometerRepository,
        TokenRepository $tokenRepository,
        Optimus $optimus,
        Stats\Data $statsModule
    ) {
        // add your dependencies
        $this->dataPointRepository = $dataPointRepository;
        $this->fermentationRepository = $fermentationRepository;
        $this->hydrometerRepository = $hydrometerRepository;
        $this->tokenRepository = $tokenRepository;
        $this->optimus = $optimus;
        $this->statsModule = $statsModule;
    }

    /**
     * @Route("/fermentations/throwaway/{fermentation}", name="throw-away")
     * @ParamConverter("fermentation")
     */
    public function __invoke(Fermentation $fermentation = null, Request $request)
    {
        if ($fermentation instanceof Fermentation) {
            return $this->renderExisting($fermentation);
        }

        $fermentation = new Fermentation();
        // set defaults
        $fermentation->setBegin(new \DateTime());
        $fermentation->setBegin(new \DateTime('+1 year'));
        $fermentation->setPublic(true);

        $form = $this->createForm(ThrowawayFermentationType::class, $fermentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fermentation->setHydrometer($this->getHydrometer());
            $this->fermentationRepository->save($fermentation);

            $this->addFlash(
                'success',
                'Fermentation was added.'
            );

            return $this->redirectToRoute('throw-away', ['fermentation' => $this->optimus->encode($fermentation->getId())]);
        }

        return $this->render('/ui/fermentations/throwaway.html.twig', [
                'form' => $form->createView(),
        ]);
    }

    /**
     * Render an existing throw-away fermentation.
     *
     */
    private function renderExisting(Fermentation $fermentation)
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
        } catch (Exception $exception) {
            return $this->render(
                'ui/exception.html.twig',
                []
            );
        }
    }

    private function getHydrometer(): Hydrometer
    {
        $hydrometer = new Hydrometer();
        $token = new Token();
        $token
            ->setType('device')
            ->setValue(bin2hex(random_bytes(10)))
        ;
        $hydrometer->setToken($token);

        $this->hydrometerRepository->save($hydrometer);
        $this->tokenRepository->save($token);

        return $hydrometer;
    }
}
