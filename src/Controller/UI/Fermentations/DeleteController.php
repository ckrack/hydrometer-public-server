<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\Fermentation;
use App\Repository\FermentationRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteController extends AbstractController
{
    protected $fermentationRepository;
    protected $logger;

    public function __construct(FermentationRepository $fermentationRepository, LoggerInterface $logger)
    {
        // add your dependencies
        $this->fermentationRepository = $fermentationRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/ui/fermentations/delete/{fermentation}", name="ui_fermentations_delete")
     * @ParamConverter("fermentation")
     */
    public function __invoke(
        Fermentation $fermentation,
        Request $request
    ) {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $fermentation);

        $form = $this->createDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->fermentationRepository->delete($fermentation);

            $this->addFlash(
                'success',
                'Fermentation was deleted.'
            );

            return $this->redirectToRoute('ui_fermentations_list');
        }

        // render the template
        return $this->render(
            '/ui/fermentations/deleteForm.html.twig',
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
