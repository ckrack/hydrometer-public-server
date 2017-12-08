<?php
namespace App\Modules\Formula;

use SpecificGravity;
use Plato;

/**
 * Formulas for Brix
 */
class Brix
{
    /**
     * the value in brix
     */
    protected $value;

    /**
     * You can start calculations by creating an object with the brix value
     * @param float $value value in brix
     */
    public function __construct(float $value = null)
    {
        $this->value = $value;
    }

    /**
     * Create Brix object with corrected conversion factor when reading from refractometer.
     * @link http://www.straighttothepint.com/specific-gravity-brix-plato-conversion-calculators/
     * @param  [type] $value            [description]
     * @param  float  $conversionFactor [description]
     * @return [type]                   [description]
     */
    public static function fromRefractometer($value, $conversionFactor = 1.04)
    {
        return (new Brix)($value / $conversionFactor);
    }

    /**
     * convert a brix value to specific gravity
     * @param  float\null $value value in brix. uses objects value if omitted.
     * @return float        specific gravity
     */
    public function toSg($value = null)
    {
        if ($value === null) {
            $value = $this->value;
        }

        // return invokable from SpecificGravity class
        return (new SpecificGravity)($value);
    }

    /**
     * calculate specific gravity to Brix
     * @param  float $specificGravity specific gravity value
     * @return float                  brix value
     */
    public function __invoke($specificGravity = null)
    {
        if ($specificGravity === null) {
            $specificGravity = $this->value;
        }

        return (((182.4601 * $specificGravity - 775.6821) * $specificGravity + 1262.7794) * $specificGravity - 669.5622);
    }
}
