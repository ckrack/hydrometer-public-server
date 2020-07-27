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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AddController extends AbstractController
{
    protected $em;

    public function __construct(
        FermentationRepository $fermentationRepository
    ) {
        // add your dependencies
        $this->fermentationRepository = $fermentationRepository;
    }

    /**
     * @Route("/ui/fermentations/add", name="ui_fermentations_add")
     */
    public function __invoke(Request $request)
    {
        $fermentation = new Fermentation();
        $fermentation->setUser($this->getUser());

        $form = $this->createForm(FermentationType::class, $fermentation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->fermentationRepository->save($fermentation);

            $this->addFlash(
                'success',
                'Fermentation was added.'
            );

            return $this->redirectToRoute('ui_fermentations_list');
        }

        // render the template
        return $this->render(
            '/ui/fermentations/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
