<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Formula\Tilt;

/**
 * Formula to convert the slightly weird timepoint format by Tilt into a Unix timestamp.
 */
class Timepoint
{
    /**
     * the value in tilt timepoint format (float).
     */
    protected $value = 0.0;

    /**
     * You can start calculations by creating an object with the timepoint value.
     *
     * @param float $value value
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * convert a timepoint value to timepoint.
     *
     * @param float\null $value value. uses objects value if omitted.
     *
     * @return float specific gravity
     */
    public function toTimestamp($value = null)
    {
        if (null === $value) {
            $value = $this->value;
        }

        // return invokable
        return (new self())($value);
    }

    /**
     * calculate timepoint to unix timestamp.
     *
     * @param float $timepoint value in timepoint
     *
     * @return float specific gravity
     */
    public function __invoke($timepoint = null)
    {
        if (null === $timepoint) {
            $timepoint = $this->value;
        }

        return round($timepoint * 24 * 60 * 60 - (25569 * 24 * 60 * 60), 0);
    }
}
