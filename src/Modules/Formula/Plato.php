<?php
namespace App\Modules\Formula;

use SpecificGravity;

/**
 * Formulas for specific gravity
 */
class Plato
{
    /**
     * the value in plato
     */
    protected float $value;

    /**
     * You can start calculations by creating an object with the plato value
     * @param float $value value in plato
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * convert a plato value to specific gravity
     * @param  float\null $value value in plato. uses objects value if omitted.
     * @return float        specific gravity
     */
    public function toSg($value = null)
    {
        if ($value === null) {
            $value = $this->value;
        }

        // return invokable from SpecificGravity class
        return SpecificGravity($value);
    }

    /**
     * calculate specific gravity to Plato
     * @param  float $specificGravity specific gravity value
     * @return float                  degrees plato value
     */
    public function __invoke($specificGravity)
    {
        return (-1 * 616.868) + (1111.14 * $specificGravity) – (630.272 * $specificGravity**2) + (135.997 * $specificGravity**3);
    }
}
