<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller;

use App\Entity\DataPoint;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\Token;
use App\Form\ThrowawayFermentationType;
use App\Modules\Stats;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Jenssegers\Optimus\Optimus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ThrowAwayFermentationController extends Controller
{
    protected $em;
    protected $statsModule;
    protected $optimus;

    public function __construct(
        EntityManagerInterface $em,
        Optimus $optimus,
        Stats\Data $statsModule
    ) {
        // add your dependencies
        $this->em = $em;
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
            $this->em->persist($fermentation);
            $this->em->flush();

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
     * @param [type] $fermentation
     */
    protected function renderExisting($fermentation)
    {
        // check for "view" access: calls all voters
        $this->denyAccessUnlessGranted('view', $fermentation);

        try {
            $latestData = $this->em->getRepository(DataPoint::class)->findByFermentation($fermentation);
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

    protected function getHydrometer(): Hydrometer
    {
        $hydrometer = new Hydrometer();
        $token = new Token();
        $token
            ->setType('device')
            ->setValue(bin2hex(random_bytes(10)))
        ;
        $hydrometer->setToken($token);

        $this->em->persist($hydrometer);
        $this->em->persist($token);

        return $hydrometer;
    }
}
