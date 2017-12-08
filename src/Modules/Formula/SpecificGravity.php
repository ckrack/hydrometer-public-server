<?php
namespace App\Modules\Formula;

use Plato;

/**
 * Formulas for specific gravity
 */
class SpecificGravity
{
    /**
     * the value in specific gravity
     */
    protected $value;

    /**
     * You can start calculations by creating an object with the sg value
     * @param float $value value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * convert a plato value to plato
     * @param  float\null $value value. uses objects value if omitted.
     * @return float        specific gravity
     */
    public function toPlato($value = null)
    {
        if ($value === null) {
            $value = $this->value;
        }

        // return invokable from SpecificGravity class
        return (new Plato)($value);
    }

    /**
     * calculate Plato to specific gravity
     * @param  float $plato value in plato
     * @return float        specific gravity
     */
    public function __invoke($plato = null)
    {
        if ($plato === null) {
            $plato = $this->value;
        }
        return 1+($plato/(258.6 - (($plato/258.2)*227.1)));
    }
}
