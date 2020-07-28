<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Stats;

use App\Entity\Calibration;
use App\Entity\Hydrometer;
use App\Repository\CalibrationRepository;
use App\Repository\DataPointRepository;
use Carbon\Carbon;

final class Data
{
    private $dataPointRepository;
    private $calibrationRepository;

    public function __construct(
        DataPointRepository $dataPointRepository,
        CalibrationRepository $calibrationRepository
    ) {
        $this->dataPointRepository = $dataPointRepository;
        $this->calibrationRepository = $calibrationRepository;
    }

    /**
     * Returns a string representing a timespan since when the value of $fieldname is stable.
     * The stability can be refined using $deviance.
     */
    public function stableSince(array $latestData, string $fieldName, float $deviance): string
    {
        // turn array around (oldest data first)
        rsort($latestData);

        if ((is_countable($latestData) ? \count($latestData) : 0) <= 1) {
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

    public function platoCombined(array $latestData, Hydrometer $hydrometer): array
    {
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
        if (!$useGravity && isset($data['dens'])) {
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
    }

    /**
     * Get an array of calibration values
     * Use the returned array with list($const1, $const2, $const3).
     */
    protected function getCalibrationValues(Hydrometer $hydrometer): array
    {
        $calibration = $this->calibrationRepository->findOneBy(['hydrometer' => $hydrometer]);

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
        $latestData = $this->dataPointRepository->findInColumns($hydrometer);

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
