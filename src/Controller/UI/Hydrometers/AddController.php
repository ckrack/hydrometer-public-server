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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddController extends Controller
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        // add your dependencies
        $this->em = $em;
    }

    /**
     * @Route("/ui/hydrometers/add", name="ui_hydrometers_add")
     */
    public function __invoke(Request $request)
    {
        $hydrometer = new Hydrometer();
        $hydrometer->setUser($this->getUser());

        $form = $this->createForm(HydrometerType::class, $hydrometer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($hydrometer);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Hydrometer was added.'
            );

            return $this->redirectToRoute('ui_hydrometers_list');
        }

        // render the template
        return $this->render(
            '/ui/hydrometers/form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
