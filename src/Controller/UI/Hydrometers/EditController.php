<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use App\Form\HydrometerType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class EditController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        // add your dependencies
        $this->em = $em;
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
            $this->em->persist($hydrometer);
            $this->em->flush();

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
