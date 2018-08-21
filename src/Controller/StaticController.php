<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Psr\Log\LoggerInterface;

class StaticController extends Controller
{
    protected $logger;

    /**
     *
     */
    public function __construct(LoggerInterface $logger)
    {
        // add your dependencies
        $this->logger = $logger;
    }

    /**
     * @Route("/{static}", defaults={"static"="about"}, name="static-page")
     */
    public function __invoke($static)
    {
        return $this->render($static.'.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
