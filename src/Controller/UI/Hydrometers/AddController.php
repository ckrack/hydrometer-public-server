<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use App\Entity\Token;
use App\Form\HydrometerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddController extends AbstractController
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

        $token = new Token();
        $token
            ->setType('device')
            ->setValue(bin2hex(random_bytes(10)))
            ->setUser($this->getUser());
        $hydrometer->setToken($token);

        $form = $this->createForm(HydrometerType::class, $hydrometer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($hydrometer);
            $this->em->persist($token);

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
