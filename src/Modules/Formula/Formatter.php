<?php
namespace App\Modules\Formula;

use App\Entity\Hydrometer;

class Formatter
{
    /**
     * [$hydrometer description]
     * @var [type]
     */
    protected $hydrometer;

    public function __construct(Hydrometer $hydrometer)
    {
        $this->hydrometer = $hydrometer;
    }

    public static function format($value, $metric = null)
    {
        switch ($metric) {
            case "V":
                return number_format($value, 2)." V";

            case "SG":
                return number_format($value, 3, ".", ".")." SG";

            case "°":
                return number_format($value, 2)." °";

            case "°C":
                return number_format($value, 2)." °C";

            case "°F":
                return number_format($value, 0)." °F";

            default:
                return number_format($value, 2)." ".$metric;
                break;
        }
    }
}
