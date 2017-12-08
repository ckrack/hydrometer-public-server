<?php
namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Modules\Auth\Token;
use App\Modules\Formula\Tilt\Timepoint;
use App\Entity;
use Exception;
use InvalidArgumentException;

class DataPoint
{
    protected $logger;
    protected $em;
    protected $tokenAuth;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Token $tokenAuth,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->tokenAuth = $tokenAuth;
        $this->logger = $logger;
    }

    /**
     * Receive datapoint for hydrometer via HTTP POST
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function post($request, $response, $args)
    {
        try {
            $data = $request->getParsedBody();
            $this->logger->debug('iHydrometer: Receive data', [$data, $args]);

            if (empty($data)) {
                $this->logger->debug('api::post: no data passed', [$args, $data]);
                throw new InvalidArgumentException('Api::post: No data passed');
            }

            if (! isset($args['token']) && ! (isset($data['ID'])) && isset($data['token'])) {
                $this->logger->debug('api::post: missing identifier', [$args, $data]);
                throw new InvalidArgumentException('Api::post: Data missing (ID or token)');
            }

            // confirm existance of the token @throws
            $authData = $this->tokenAuth->authenticate(empty($args['token']) ? $data['token'] : $args['token']);

            $hydrometer = $this->em->getRepository(Entity\Hydrometer::class)->find($authData['hydrometer_id']);

            $this->logger->debug('iHydrometer: Receive data for Hydrometer', [$hydrometer, $data]);

            // data needs to be changed possibly?
            $data = $this->prepareData($data);

            $dataPoint = new Entity\DataPoint;

            // prevent overwriting the ID by unsetting the espId
            if (isset($data['id'])) {
                unset($data['id']);
            }

            $dataPoint->import($data);
            $dataPoint->setHydrometer($hydrometer);

            $this->em->persist($hydrometer);
            $this->em->persist($dataPoint);

            $this->em->flush();

            return $response
                ->withStatus(200);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $response
                ->withStatus(500);
        }
    }

    /**
     * Prepare data for the import into Entity
     * @param  array $data [description]
     * @return array       [description]
     */
    protected function prepareData($data)
    {
        switch (true) {
            // Tilt
            case isset($data['Timepoint']):
                $transformedData = [
                    'temperature' => $data['Temp'],
                    'gravity' => $data['SG']
                ];
                return $transformedData;
            default:
                return $data;
        }
    }
}
