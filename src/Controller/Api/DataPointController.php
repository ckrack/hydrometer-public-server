<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManagerInterface;
use App\Modules\Auth\Token;
use App\Entity;
use App\Modules\Formula\Tilt\Timepoint;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class DataPointController extends Controller
{
    protected $em;

    /**
     *
     */
    public function __construct(
        EntityManagerInterface $em,
        Token $tokenAuth,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->tokenAuth = $tokenAuth;
        $this->logger = $logger;
    }

    /**
     * Receive datapoint for hydrometer via HTTP POST.
     * @Route("/api/ispindel/{token}", name="api-post-spindle")
     * @Route("/api/tilt/{token}", name="api-post-tilt")
     */
    public function __invoke()
    {
        try {
            $data = $request->getContent();
            if ($data) {
                $data = json_decode($data);
            }
            $this->logger->debug('Spindle: Receive data', [$data, $args]);

            if (empty($data)) {
                $this->logger->debug('api::post: no data passed', [$args, $data]);
                throw new InvalidArgumentException('Api::post: No data passed');
            }

            if (!isset($args['token']) && !(isset($data['ID'])) && isset($data['token'])) {
                $this->logger->debug('api::post: missing identifier', [$args, $data]);
                throw new InvalidArgumentException('Api::post: Data missing (ID or token)');
            }

            // confirm existance of the token @throws
            $authData = $this->tokenAuth->authenticate(empty($args['token']) ? $data['token'] : $args['token']);

            $hydrometer = $this->em->getRepository(Entity\Hydrometer::class)->find($authData['hydrometer_id']);

            $this->logger->debug('Spindle: Receive data for Hydrometer', [$hydrometer, $data]);

            // data needs to be changed possibly?
            $data = $this->prepareData($data);

            $dataPoint = new Entity\DataPoint();

            $dataPoint->import($data);
            $dataPoint->setHydrometer($hydrometer);

            $this->em->persist($hydrometer);
            $this->em->persist($dataPoint);

            $this->em->flush();

            return new Response('', 200);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return new Response('', 500);
        }
    }

    /**
     * Prepare data for the import into Entity.
     *
     * @param array $data [description]
     *
     * @return array [description]
     */
    protected function prepareData(array $data): array
    {
        // prevent overwriting the ID by unsetting the espId
        if (isset($data['id'])) {
            unset($data['id']);
        }

        switch (true) {
            // TILT
            case isset($data['Timepoint']):
                $transformedData = [
                    'temperature' => $data['Temp'],
                    'gravity' => $data['SG'],
                ];

                return $transformedData;
            default:
                return $data;
        }
    }
}
