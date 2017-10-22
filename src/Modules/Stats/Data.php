<?php
namespace App\Modules\Stats;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use App\Entity\Hydrometer;
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

    public function platoCombined(array $latestData, Hydrometer $hydrometer)
    {
        list($const1, $const2, $const3, $isCalibrated) = $this->getCalibrationValues($hydrometer);

        // flag to indicate whether there are gravity values.
        // this indicates that the new firmware is used (>= 4.0)
        $useGravity = false;

        $data = [];

        foreach ($latestData as $value) {
            $dens = $const1 * $value['angle'] ** 2 - $const2 * $value['angle'] + $const3;
            $data['dens'][] = $dens;
            foreach ($value as $unit => $v) {
                $data[$unit][] = $v;

                if ($unit === 'gravity' && $v) {
                    $useGravity = true;
                }
            }
        }

        // use the flag to overwrite old data
        if (false === $useGravity) {
            $data['gravity'] = $data['dens'];
        }
        unset($data['dens']);
        unset($data['groupTime']);

        // render template
        return [
            'name' => $hydrometer->getName(),
            'data' => $data,
            'isCalib' => $isCalibrated
        ];
    }

    /**
     * Get an array of calibration values
     * Use the returned array with list($const1, $const2, $const3)
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    protected function getCalibrationValues(Hydrometer $hydrometer)
    {
        $calibration = $this->em->getRepository('App\Entity\Calibration')->findOneBy(['hydrometer' => $hydrometer]);

        $values = [0, 0, 0, false];

        if ($calibration instanceof Calibration) {
            $values = [
                $calibration->getConst1(),
                $calibration->getConst2(),
                $calibration->getConst3(),
                true
            ];
        }
        return $values;
    }

    /**
     * get angle and temperature values
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function angle(Hydrometer $hydrometer)
    {
        $latestData = $this->em->getRepository('App\Entity\DataPoint')->findInColumns($hydrometer);

        $data = [];
        foreach ($latestData as $value) {
            foreach ($value as $unit => $v) {
                $data[$unit][] = $v;
            }
        }

        unset($data['groupTime']);

        // render template
        return [
            'name' => $hydrometer->getName(),
            'data' => $data
        ];
    }
}

