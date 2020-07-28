<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class StaticController extends AbstractController
{
    /**
     * @Route("/{static}", defaults={"static": "about"}, name="static-page")
     * @Route("/logout", defaults={"static": "logout"}, name="logout")
     */
    public function __invoke($static)
    {
        return $this->render($static.'.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
