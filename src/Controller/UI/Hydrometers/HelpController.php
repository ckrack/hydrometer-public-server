<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Hydrometers;

use App\Entity\Hydrometer;
use App\Security\Voter\OwnerVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class HelpController extends AbstractController
{
    /**
     * @Route("/ui/hydrometers/help/{hydrometer}", name="ui_hydrometers_help")
     * @ParamConverter("hydrometer")
     */
    public function __invoke(Hydrometer $hydrometer)
    {
        $this->denyAccessUnlessGranted(OwnerVoter::VIEW, $hydrometer);

        return $this->render('ui/hydrometers/help.html.twig', [
            'hydrometer' => $hydrometer,
        ]);
    }
}
