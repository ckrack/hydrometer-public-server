<?php
namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Modules\Auth\PasswordLess;
use App\Entity;

class DataPoint
{
    protected $logger;
    protected $em;
    protected $passwordLess;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        PasswordLess $passwordLess,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->passwordLess = $passwordLess;
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
                throw new \InvalidArgumentException('Api::post: No data passed');
            }

            if (! isset($args['token']) && ! (isset($data['ID'])) && isset($data['userToken'])) {
                $this->logger->debug('api::post: missing identifier', [$args, $data]);
                throw new \InvalidArgumentException('Api::post: Data missing (ID or token)');
            }

            // confirm existance of the token
            $user = $this->passwordLess->confirm(empty($args['token']) ? $data['userToken'] : $args['token'], null, null, 'device', '10 years ago');
            $this->logger->debug('api::post: by user', [$user]);

            if (! $user instanceof \App\Entity\User) {
                throw new \Exception("Could not confirm token", 1);
            }

            $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->getOrCreate($data['ID'], $user, empty($args['token']) ? $data['userToken'] : $args['token']);

            // set token and user on the hydrometer
            $hydrometer->setUser($this->getEntityManager()->getRepository('App\Entity\User')->find($user->getId()));
            $hydrometer->setToken($this->getEntityManager()->getRepository('App\Entity\Token')->findOneBy(['value' => $token]));

            // set the hydrometer name if specified
            if (isset($data['name'])) {
                $hydrometer->setName($data['name']);
            }

            $this->logger->debug('iHydrometer: Receive data for Hydrometer', [$hydrometer, $data]);

            $dataPoint = new Entity\DataPoint;

            // prevent overwriting the ID by unsetting the espId
            unset($data['id']);

            $dataPoint->import($data);
            $dataPoint->setHydrometer($hydrometer);

            $this->em->persist($hydrometer);
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
            $hydrometer = null;
            if (isset($args['hydrometer'])) {
                $hydrometer = $this->em->find('App\Entity\Hydrometer', $args['hydrometer']);
            }
            $this->logger->debug('Api:Data: Find data', [$hydrometer, $args]);

            $dataPoints = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($hydrometer);

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
