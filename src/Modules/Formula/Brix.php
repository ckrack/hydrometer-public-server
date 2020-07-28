<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Formula;

/**
 * Formulas for Brix.
 */
final class Brix
{
    /**
     * the value in brix.
     */
    private $value;

    /**
     * You can start calculations by creating an object with the brix value.
     *
     * @param float $value value in brix
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * Create Brix object with corrected conversion factor when reading from refractometer.
     *
     * @see http://www.straighttothepint.com/specific-gravity-brix-plato-conversion-calculators/
     *
     * @param [type] $value            [description]
     * @param float  $conversionFactor [description]
     *
     * @return [type] [description]
     */
    public static function fromRefractometer($value, $conversionFactor = 1.04)
    {
        return (new self())($value / $conversionFactor);
    }

    /**
     * convert a brix value to specific gravity.
     *
     * @param float\null $value value in brix. uses objects value if omitted.
     *
     * @return float specific gravity
     */
    public function toSg($value = null)
    {
        if (null === $value) {
            $value = $this->value;
        }

        // return invokable from SpecificGravity class
        return (new SpecificGravity())($value);
    }

    /**
     * calculate specific gravity to Brix.
     *
     * @param float $specificGravity specific gravity value
     *
     * @return float brix value
     */
    public function __invoke($specificGravity = null)
    {
        if (null === $specificGravity) {
            $specificGravity = $this->value;
        }

        return ((182.4601 * $specificGravity - 775.6821) * $specificGravity + 1262.7794) * $specificGravity - 669.5622;
    }
}
