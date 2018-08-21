<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Fermentations;

use App\Entity\Fermentation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends Controller
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        // add your dependencies
        $this->em = $em;
    }

    /**
     * List of fermentations.
     *
     * @Route("/ui/fermentations", name="ui_fermentations_list")
     */
    public function __invoke()
    {
        try {
            $user = $this->getUser();

            $data = $this->em->getRepository(Fermentation::class)->findAllByUser($user);

            // render template
            return $this->render(
                '/ui/fermentations/list.html.twig',
                [
                    'data' => $data,
                    'user' => $user,
                    'form' => $this->createDeleteForm()->createView(),
                ]
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->render(
                'ui/exception.html.twig'
            );
        }
    }

    /**
     * Creates a form to delete a fermentation.
     */
    private function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
