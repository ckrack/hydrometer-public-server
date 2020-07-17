<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Stats;

use Psr\Log\LoggerInterface;

final class AnomalyFixed
{
    /**
     * our sample size.
     *
     * @var array
     */
    protected $outlier;
    protected $mean;
    protected $previous = null;
    protected $std;
    protected $logger;

    public function __construct(
        $std,
        $mean,
        $outlierFactor = 3,
        LoggerInterface $logger = null
    ) {
        $this->outlier = $outlierFactor * $std;
        $this->mean = $mean;
        $this->logger = $logger;
    }

    public function setStdDev($std)
    {
        $this->std = $std;
    }

    public function is($value)
    {
        // if the value is bigger than the outlier, we have an anomaly
        return abs($value - $this->mean) > $this->outlier;
    }
}
