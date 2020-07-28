<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\Fermentation;
use App\Form\FermentationType;
use App\Repository\FermentationRepository;
use App\Security\Voter\OwnerVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class EditController extends AbstractController
{
    protected $fermentationRepository;

    public function __construct(
        FermentationRepository $fermentationRepository
    ) {
        // add your dependencies
        $this->fermentationRepository = $fermentationRepository;
    }

    /**
     * @Route("/ui/fermentations/edit/{fermentation}", name="ui_fermentations_edit")
     * @ParamConverter("fermentation")
     */
    public function __invoke(
        Fermentation $fermentation,
        Request $request
    ) {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted(OwnerVoter::EDIT, $fermentation);

        $form = $this->createForm(FermentationType::class, $fermentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->fermentationRepository->save($fermentation);

            $this->addFlash(
                'success',
                'Fermentation was edited.'
            );

            return $this->redirectToRoute('ui_fermentations_list');
        }

        // render the template
        return $this->render(
            '/ui/fermentations/editForm.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
