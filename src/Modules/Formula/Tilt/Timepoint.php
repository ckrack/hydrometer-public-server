<?php
namespace App\Modules\Formula\Tilt;

/**
 * Formula to convert the slightly weird
 */
class Timepoint
{
    /**
     * the value in tilt timepoint format (float)
     */
    protected $value = 0.0;

    /**
     * You can start calculations by creating an object with the timepoint value
     * @param float $value value
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * convert a timepoint value to timepoint
     * @param  float\null $value value. uses objects value if omitted.
     * @return float        specific gravity
     */
    public function toTimestamp($value = null)
    {
        if ($value === null) {
            $value = $this->value;
        }

        // return invokable
        return Timepoint($value);
    }

    /**
     * calculate timepoint to unix timestamp
     * @param  float $timepoint value in timepoint
     * @return float        specific gravity
     */
    public function __invoke($timepoint)
    {
        return round($timepoint * 24 * 60 * 60 - (25569 * 24 * 60 * 60), 0);
    }
}
