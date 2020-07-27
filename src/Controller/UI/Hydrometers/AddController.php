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
use App\Repository\HydrometerRepository;
use App\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AddController extends AbstractController
{
    protected $hydrometerRepository;
    protected $tokenRepository;

    public function __construct(HydrometerRepository $hydrometerRepository, TokenRepository $tokenRepository)
    {
        // add your dependencies
        $this->hydrometerRepository = $hydrometerRepository;
        $this->tokenRepository = $tokenRepository;
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
            $this->hydrometerRepository->save($hydrometer);
            $this->tokenRepository->save($token);

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
