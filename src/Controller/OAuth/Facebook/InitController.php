<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth\Facebook;

use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class InitController extends Controller
{
    public function __construct(
        EntityManagerInterface $em,
        ClientRegistry $oauthRegistry,
        LoggerInterface $logger
    ) {
        // add your dependencies
        $this->em = $em;
        $this->oauthRegistry = $oauthRegistry;
        $this->logger = $logger;
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
                ['public_profile', 'email']
            )
        ;
    }
}
