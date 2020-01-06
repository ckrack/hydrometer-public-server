<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Datapoints;

use App\Entity\DataPoint;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends Controller
{
    protected $em;
    protected $logger;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @Route("/ui/datapoints/delete/{datapoint}", name="ui_datapoints_delete")
     * @ParamConverter("datapoint")
     */
    public function __invoke(
        DataPoint $datapoint,
        Request $request
    ) {
        // check for "edit" access: calls all voters
        $this->denyAccessUnlessGranted('edit', $datapoint);

        try {
            $form = $this->createDeleteForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->remove($datapoint);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    'Datapoint was deleted.'
                );

                return $this->redirectToRoute('ui_datapoints_list');
            }

            // render the template
            return $this->render(
                '/ui/datapoints/deleteForm.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
        } catch (\Exception $e) {
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
