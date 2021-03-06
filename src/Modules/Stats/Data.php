<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Stats;

use App\Entity\Calibration;
use App\Entity\DataPoint;
use App\Entity\Hydrometer;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class Data
{
    protected $view;
    protected $logger;
    protected $em;

    public function __construct(
        EntityManagerInterface $em,
        \Twig\Environment $view,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * Returns a string representing a timespan since when the value of $fieldname is stable.
     * The stability can be refined using $deviance.
     *
     * @param array $latestData [description]
     * @param string $fieldName  [description]
     * @param float $deviance   [description]
     *
     * @return string [description]
     */
    public function stableSince($latestData, $fieldName, $deviance)
    {
        // turn array around (oldest data first)
        rsort($latestData);

        if ((is_countable($latestData) ? count($latestData) : 0) <= 1) {
            return _('Not yet');
        }

        // use first value as initial
        $initial = $latestData[0][$fieldName];

        $stableSince = null;

        foreach ($latestData as $k => $values) {
            // go through datasets and mark the first dataset with a difference higher than $deviance
            if (abs($values[$fieldName] - $initial) > $deviance) {
                $stableSince = $k;
                break;
            }
        }

        if ($stableSince === null) {
            return _('Not yet');
        }

        return Carbon::parse($latestData[0]['time'])->diffForHumans($latestData[$stableSince]['time'], Carbon::DIFF_ABSOLUTE);
    }

    /**
     * [platoCombined description].
     *
     * @param array      $latestData [description]
     * @param Hydrometer $hydrometer [description]
     *
     * @return array [description]
     */
    public function platoCombined(array $latestData, Hydrometer $hydrometer)
    {
        try {
            [$const1, $const2, $const3, $isCalibrated] = $this->getCalibrationValues($hydrometer);

            // flag to indicate whether there are gravity values.
            // this indicates that the new firmware is used (>= 4.0)
            $useGravity = false;

            $data = [];

            foreach ($latestData as $value) {
                $dens = $const1 * $value['angle'] ** 2 - $const2 * $value['angle'] + $const3;
                $data['dens'][] = $dens;
                foreach ($value as $unit => $v) {
                    $data[$unit][] = $v;

                    if ('gravity' === $unit && $v) {
                        $useGravity = true;
                    }
                }
            }

            // use the flag to overwrite old data
            if (false === $useGravity && isset($data['dens'])) {
                $data['gravity'] = $data['dens'];
            }
            unset($data['dens']);
            unset($data['groupTime']);

            // render template
            return [
                'name' => $hydrometer->getName(),
                'data' => $data,
                'isCalib' => $isCalibrated,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get an array of calibration values
     * Use the returned array with list($const1, $const2, $const3).
     *
     * @param Hydrometer $hydrometer [description]
     *
     * @return [type] [description]
     */
    protected function getCalibrationValues(Hydrometer $hydrometer)
    {
        $calibration = $this->em->getRepository(Calibration::class)->findOneBy(['hydrometer' => $hydrometer]);

        $values = [0, 0, 0, false];

        if ($calibration instanceof Calibration) {
            $values = [
                $calibration->getConst1(),
                $calibration->getConst2(),
                $calibration->getConst3(),
                true,
            ];
        }

        return $values;
    }

    /**
     * get angle and temperature values.
     *
     * @param Hydrometer $hydrometer [description]
     *
     * @return [type] [description]
     */
    public function angle(Hydrometer $hydrometer)
    {
        $latestData = $this->em->getRepository(DataPoint::class)->findInColumns($hydrometer);

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
            'data' => $data,
        ];
    }
}
