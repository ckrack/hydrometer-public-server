<?php
namespace App\Modules\Stats;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use App\Entity\Spindle;
use App\Entity\Calibration;
use App\Entity\DataPoint;

class Data
{
    protected $view;
    protected $logger;
    protected $em;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->view = $view;
        $this->logger = $logger;
    }

    public function status(Spindle $spindle = null)
    {
        if ($spindle === null) {
            $spindle = $this->em->getRepository('App\Entity\Spindle')->getLastActive();
        }

        $latestData = $this->em->getRepository('App\Entity\Spindle')->getLatestData($spindle);

        $this->logger->debug('iSpindle: Latest data', (array) $latestData);
        return $latestData;
    }

    public function plato4($spindle)
    {
        if ($spindle === null) {
            $spindle = $this->em->getRepository('App\Entity\Spindle')->getLastActive();
        }

        $calibration = $this->em->getRepository('App\Entity\Calibration')->findOneBy(['spindle' => $spindle]);

        $const1 = 0;
        $const2 = 0;
        $const3 = 0;

        if ($calibration instanceof Calibration) {
            $const1 = $calibration->getConst1();
            $const2 = $calibration->getConst2();
            $const3 = $calibration->getConst3();
        }

        $latestData = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($spindle);

        // add first row
        $keys = array_keys(array_pop($latestData));

        $data = [];
        foreach ($latestData as $value) {
            $dens = $const1 * $value['angle'] ** 2 - $const2 * $value['angle'] + $const3;
            $data['dens'][] = $dens;
            foreach ($value as $unit => $v) {
                $data[$unit][] = $v;
            }
        }

        // render template
        return [
            'name' => $spindle->getName(),
            'data' => $data,
            'isCalib' => is_a($calibration, '\App\Entity\Calibration')
        ];
    }

    public function plato($spindle)
    {
        if ($spindle === null) {
            $spindle = $this->em->getRepository('App\Entity\Spindle')->getLastActive();
        }
        $latestData = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($spindle);

        // add first row
        $keys = array_keys(array_pop($latestData));

        $data = [];
        foreach ($latestData as $value) {
            foreach ($value as $unit => $v) {
                $data[$unit][] = $v;
            }
        }

        // render template
        return [
            'name' => $spindle->getName(),
            'data' => $data
        ];
    }

    public function angle($spindle)
    {
        if ($spindle === null) {
            $spindle = $this->em->getRepository('App\Entity\Spindle')->getLastActive();
        }

        $latestData = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($spindle);

        // add first row
        $keys = array_keys(array_pop($latestData));

        $data = [];
        foreach ($latestData as $value) {
            foreach ($value as $unit => $v) {
                $data[$unit][] = $v;
            }
        }

        // render template
        return [
            'name' => $spindle->getName(),
            'data' => $data
        ];
    }
}

