<?php
namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Entity;

class DataPoint
{
    protected $logger;
    protected $em;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Receive datapoint for spindle via HTTP POST
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function post($request, $response, $args)
    {
        try {
            $data = $request->getParsedBody();
            $this->logger->debug('iSpindle: Receive data', [$data]);

            if (empty($data)) {
                throw new \InvalidArgumentException('Api::post: No data passed');
            }

            if (! isset($data['id'])) {
                throw new \InvalidArgumentException('Api::post: Data missing (id)');
            }

            $spindle = $this->em->getRepository('App\Entity\Spindle')->getOrCreate($data['id']);

            // set the spindle name if specified
            if (isset($data['name'])) {
                $spindle->setName($data['name']);
            }

            $this->logger->debug('iSpindle: Receive data for Spindle', [$spindle, $data]);

            $dataPoint = new Entity\DataPoint;

            $methods = get_class_methods(get_class($dataPoint));

            // prevent overwriting the id by unsetting the espId
            unset($data['id']);

            $dataPoint->import($data);
            $dataPoint->setSpindle($spindle);

            $this->em->persist($spindle);
            $this->em->persist($dataPoint);

            $this->em->flush();

            return $response
                ->withStatus(200);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $response
                ->withStatus(500);
        }
    }

    /**
     * Get datapoints
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function get($request, $response, $args)
    {
        try {
            $spindle = null;
            if (isset($args['spindle'])) {
                $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
            }
            $this->logger->debug('Api:Data: Find data', [$spindle, $args]);

            $dataPoints = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($spindle);

            // add first row
            $keys = array_keys(array_pop($dataPoints));

            $dataJson = new \stdClass;
            foreach ($dataPoints as $value) {
                foreach ($value as $unit => $v) {
                    $dataJson->{$unit}[] = $v;
                }
            }

            return $response->withJson($dataJson);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $response
                ->withStatus(500);
        }
    }
}
