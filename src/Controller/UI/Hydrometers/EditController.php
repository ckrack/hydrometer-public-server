<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use App\Form\HydrometerType;
use App\Repository\HydrometerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class EditController extends AbstractController
{
    protected $hydrometerRepository;

    public function __construct(HydrometerRepository $hydrometerRepository)
    {
        // add your dependencies
        $this->hydrometerRepository = $hydrometerRepository;
    }

    /**
     * @Route("/ui/hydrometers/edit/{hydrometer}", name="ui_hydrometers_edit")
     * @ParamConverter("hydrometer")
     */
    public function __invoke(
        Hydrometer $hydrometer,
        Request $request
    ) {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $hydrometer);

        $form = $this->createForm(HydrometerType::class, $hydrometer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->hydrometerRepository->save($hydrometer);

            $this->addFlash(
                'success',
                'Hydrometer was edited.'
            );

            return $this->redirectToRoute('ui_hydrometers_list');
        }

        // render the template
        return $this->render(
            '/ui/hydrometers/editForm.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
