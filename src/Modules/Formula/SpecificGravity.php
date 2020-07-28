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
class SpecificGravity
{
    /**
     * the value in specific gravity.
     */
    private $value;

    /**
     * You can start calculations by creating an object with the sg value.
     *
     * @param float $value value
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * convert a plato value to plato.
     *
     * @param float\null $value value. uses objects value if omitted.
     *
     * @return float specific gravity
     */
    public function toPlato($value = null)
    {
        if (null === $value) {
            $value = $this->value;
        }

        // return invokable from SpecificGravity class
        return (new Plato())($value);
    }

    /**
     * calculate Plato to specific gravity.
     *
     * @param float $plato value in plato
     *
     * @return float specific gravity
     */
    public function __invoke($plato = null)
    {
        if (null === $plato) {
            $plato = $this->value;
        }

        return 1 + ($plato / (258.6 - (($plato / 258.2) * 227.1)));
    }
}
