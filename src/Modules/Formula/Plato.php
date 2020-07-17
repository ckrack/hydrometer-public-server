<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Formula;

/**
 * Formulas for specific gravity.
 */
final class Plato
{
    /**
     * the value in plato.
     */
    protected $value;

    /**
     * You can start calculations by creating an object with the plato value.
     *
     * @param float $value value in plato
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * convert a plato value to specific gravity.
     *
     * @param float\null $value value in plato. uses objects value if omitted.
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
     * calculate specific gravity to Plato.
     *
     * @param float $specificGravity specific gravity value
     *
     * @return float degrees plato value
     */
    public function __invoke($specificGravity = null)
    {
        if (null === $specificGravity) {
            $specificGravity = $this->value;
        }

        return (-1 * 616.868) + (1111.14 * $specificGravity) - (630.272 * $specificGravity ** 2) + (135.997 * $specificGravity ** 3);
    }
}
