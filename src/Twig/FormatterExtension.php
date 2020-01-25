<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Twig;

use App\Modules\Formula\Formatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatterExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format', [$this, 'formatFilter']),
            new TwigFilter('roundto', [$this, 'roundToFilter']),
            new TwigFilter('treshold', [$this, 'tresholdFilter']),
        ];
    }

    /**
     * Returns value formatted by metric.
     *
     * @return string [description]
     */
    public function formatFilter($value, $metric = null)
    {
        return Formatter::format($value, $metric);
    }

    /**
     * Returns value to which we should round by metric.
     *
     * @param string $metric [description]
     *
     * @return int [description]
     */
    public function roundToFilter($metric = null): int
    {
        return Formatter::roundTo($metric);
    }

    /**
     * Returns array with tresholds for metric.
     *
     * @param string $metric [description]
     *
     * @return array [description]
     */
    public function tresholdFilter($metric = null)
    {
        return Formatter::treshold($metric);
    }
}
