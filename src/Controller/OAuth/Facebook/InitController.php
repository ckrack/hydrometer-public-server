<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth\Facebook;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class InitController extends AbstractController
{
    private $oauthRegistry;

    public function __construct(
        ClientRegistry $oauthRegistry
    ) {
        $this->oauthRegistry = $oauthRegistry;
    }

    /**
     * Send the visitor to facebooks oauth endpoint to enquire authentication.
     *
     * @Route("/auth/init/facebook", name="connect_facebook_init")
     */
    public function __invoke()
    {
        // will redirect to Facebook!
        return $this->oauthRegistry
            ->getClient('facebook') // key used in config.yml
            ->redirect(
                ['public_profile', 'email'],
                []
            )
        ;
    }
}
