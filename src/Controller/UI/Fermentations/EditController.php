<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\Fermentation;
use App\Form\FermentationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class EditController extends AbstractController
{
    protected $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        // add your dependencies
        $this->em = $em;
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
        $this->denyAccessUnlessGranted('edit', $fermentation);

        $form = $this->createForm(FermentationType::class, $fermentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($fermentation);
            $this->em->flush();

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
