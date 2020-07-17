<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth\Google;

use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class InitController extends AbstractController
{
    protected $em;
    protected $oauthRegistry;
    protected $logger;

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
     * Send the visitor to googles oauth endpoint to enquire authentication.
     *
     * @Route("/auth/init/google", name="connect_google_init")
     */
    public function __invoke()
    {
        // will redirect to Google!
        return $this->oauthRegistry
            ->getClient('google') // key used in config.yml
            ->redirect(
                ['profile', 'email']
            )
        ;
    }
}
