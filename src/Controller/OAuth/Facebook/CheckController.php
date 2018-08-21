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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CheckController extends Controller
{
    protected $oauthRegistry;

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
     * @Route("/auth/check/facebook", name="connect_facebook_check")
     */
    public function __invoke(Request $request)
    {
        // we use guard for the auth. if we reach this, we are logged in.
        // add a welcome flash message?
        return $this->redirect($this->generateUrl('ui_hydrometers_list'));
    }
}
