<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class HelpController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        // add your dependencies
        $this->em = $em;
    }

    /**
     * @Route("/ui/hydrometers/help/{hydrometer}", name="ui_hydrometers_help")
     * @ParamConverter("hydrometer")
     */
    public function __invoke(Hydrometer $hydrometer)
    {
        // check for "view" access: calls all voters
        $this->denyAccessUnlessGranted('view', $hydrometer);

        return $this->render('ui/hydrometers/help.html.twig', [
            'hydrometer' => $hydrometer,
        ]);
    }
}
