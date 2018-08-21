<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Formula;

use App\Entity\Hydrometer;

class Formatter
{
    /**
     * [$hydrometer description].
     *
     * @var [type]
     */
    protected $hydrometer;

    public function __construct(Hydrometer $hydrometer)
    {
        $this->hydrometer = $hydrometer;
    }

    public static function format($value, $metric = null)
    {
        $value = (float) $value;
        switch ($metric) {
            case 'V':
                return number_format($value, 2).' V';

            case 'SG':
                return number_format($value, 3, '.', '.').' SG';

            case '°P':
                return number_format($value, 2).' °P';

            case '°':
                return number_format($value, 2).' °';

            case '°C':
                return number_format($value, 2).' °C';

            case '°F':
                return number_format($value, 0).' °F';

            default:
                return number_format($value, 2).' '.$metric;
                break;
        }
    }

    public static function roundTo($metric = null)
    {
        switch ($metric) {
            case 'V':
                return 2;

            case 'SG':
                return 3;

            case '°':
                return 2;

            case '°C':
                return 2;

            case '°F':
                return 0;

            default:
                return 2;
                break;
        }
    }

    public static function treshold($metric = null)
    {
        switch ($metric) {
            case 'V':
                return [2.7, 3.1, 3.5, 4.5];

            case '°C':
                return [-2.5, 8, 14, 22, 26, 35];

            case '°F':
                return [27, 46, 57, 72, 79, 100];

            case 'SG':
                return [1.00, 1.03, 1.06, 1.1, 1.15, 1.18];

            case '°P':
                return [0, 5, 10, 15, 20, 40];

            default:
                return [0];
        }
    }
}
