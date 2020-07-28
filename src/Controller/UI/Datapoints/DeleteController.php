<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI\Datapoints;

use App\Entity\DataPoint;
use App\Repository\DataPointRepository;
use App\Security\Voter\DataPointOwnerVoter;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteController extends AbstractController
{
    private $dataPointRepository;
    private $logger;

    public function __construct(
        DataPointRepository $dataPointRepository,
        LoggerInterface $logger
    ) {
        $this->dataPointRepository = $dataPointRepository;
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
        $this->denyAccessUnlessGranted(DataPointOwnerVoter::EDIT, $datapoint);

        try {
            $form = $this->createDeleteForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->dataPointRepository->delete($datapoint);

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
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

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
