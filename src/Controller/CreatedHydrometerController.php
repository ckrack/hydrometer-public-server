<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

class CreatedHydrometerController extends AbstractController
{
    #[Route('/created/{token}', name: 'app_created_hydrometer')]
    public function __invoke(Ulid $token): Response
    {
        return $this->render('hydrometer/new_hydrometer.html.twig', [
            'hydrometer_id' => $token,
        ]);
    }
}
