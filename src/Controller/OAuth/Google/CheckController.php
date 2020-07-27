<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth\Google;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CheckController extends AbstractController
{
    /**
     * @Route("/auth/check/google", name="connect_google_check")
     */
    public function __invoke(Request $request)
    {
        // we use guard for the auth. if we reach this, we are logged in.
        return $this->redirect($this->generateUrl('ui_hydrometers_list'));
    }
}
