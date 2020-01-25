<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Stats;

use Psr\Log\LoggerInterface;

class Anomaly
{
    /**
     * our sample size.
     *
     * @var array
     */
    protected $sample = [];
    protected $factor;
    protected $std;
    protected $mean;
    protected $previous = null;
    protected $logger;

    public function __construct(
        $outlierFactor = 3,
        LoggerInterface $logger = null
    ) {
        $this->factor = $outlierFactor;
        $this->logger = $logger;
    }

    public function setStdDev($std)
    {
        $this->std = $std;
    }

    public function is($value)
    {
        // save new value as sample
        array_unshift($this->sample, $value);

        $count = count($this->sample);

        if ($count <= 2) {
            $this->logger->debug('Anomaly: sample too small');

            return false;
        }

        $mean = $this->mean ?: array_sum($this->sample) / $count;

        $stddev = $this->std ?: stats_standard_deviation($this->sample, $value);

        $outlier = $this->factor * $stddev;

        if ($count > 20) {
            array_pop($this->sample);
            $this->logger->debug('Anomaly: sample popped', $this->sample);
        }

        // if the value is bigger than the outlier, we have an anomaly
        if (abs($value - $mean) > $outlier) {
            $this->logger->debug('Anomaly: check', [$mean, $outlier, abs($value - $mean), (abs($value - $mean) - $outlier)]);

            return true;
        }

        return false;
    }
}

if (!function_exists('stats_standard_deviation')) {
    /**
     * This user-land implementation follows the implementation quite strictly;
     * it does not attempt to improve the code or algorithm in any way. It will
     * raise a warning if you have fewer than 2 values in your array, just like
     * the extension does (although as an E_USER_WARNING, not E_WARNING).
     *
     * @param bool $sample [optional] Defaults to false
     *
     * @return float|bool the standard deviation or false on error
     */
    function stats_standard_deviation(array $a, $sample = false)
    {
        $n = count($a);
        if (0 === $n) {
            trigger_error('The array has zero elements', E_USER_WARNING);

            return false;
        }
        if ($sample && 1 === $n) {
            trigger_error('The array has only 1 element', E_USER_WARNING);

            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((float) $val) - $mean;
            $carry += $d * $d;
        }
        if ($sample) {
            --$n;
        }

        return sqrt($carry / $n);
    }
}
