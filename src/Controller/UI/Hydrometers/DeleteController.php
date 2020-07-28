<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use App\Repository\HydrometerRepository;
use App\Security\Voter\OwnerVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteController extends AbstractController
{
    protected $hydrometerRepository;

    public function __construct(HydrometerRepository $hydrometerRepository)
    {
        // add your dependencies
        $this->hydrometerRepository = $hydrometerRepository;
    }

    /**
     * @Route("/ui/hydrometers/delete/{hydrometer}", name="ui_hydrometers_delete")
     * @ParamConverter("hydrometer")
     */
    public function __invoke(
        Hydrometer $hydrometer,
        Request $request
    ) {
        $this->denyAccessUnlessGranted(OwnerVoter::EDIT, $hydrometer);

        $form = $this->createDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->hydrometerRepository->delete($hydrometer);

            $this->addFlash(
                'success',
                'Hydrometer was deleted.'
            );

            return $this->redirectToRoute('ui_hydrometers_list');
        }

        // render the template
        return $this->render(
            '/ui/hydrometers/deleteForm.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
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
