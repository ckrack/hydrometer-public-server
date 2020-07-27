<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth\Google;

use Symfony\Component\Routing\Annotation\Route;

final class InitController
{
    /**
     * Send the visitor to googles oauth endpoint to enquire authentication.
     *
     * @Route("/auth/init/google", name="connect_google_init")
     */
    public function __invoke()
    {
        // will redirect to Google!
        return $this->oauthRegistry
            ->getClient('google')
            ->redirect(
                ['profile', 'email'],
                []
            )
        ;
    }
}
