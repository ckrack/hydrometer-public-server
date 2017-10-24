<?php
namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Entity\DataPoint;

class Hydrometer
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
     * Receive datapoint for hydrometer via HTTP POST
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function get($request, $response, $args)
    {
        try {
            $hydrometers = $this->em->getRepository('App\Entity\Hydrometer')->findAll();

            return json_encode($hydrometers);

            return $response
                ->withStatus(200);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $response
                ->withStatus(500);
        }
    }
}
