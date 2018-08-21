<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Modules\Formula\Formatter;

class FormatterExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('format', array($this, 'formatFilter')),
            new TwigFilter('roundto', array($this, 'roundToFilter')),
            new TwigFilter('treshold', array($this, 'tresholdFilter')),
        );
    }

    /**
     * Returns value formatted by metric
     * @param  string $number [description]
     * @return string         [description]
     */
    public function formatFilter($value, $metric = null)
    {
        return Formatter::format($value, $metric);
    }

    /**
     * Returns value to which we should round by metric
     * @param  string $metric [description]
     * @return integer         [description]
     */
    public function roundToFilter($metric = null) : int
    {
        return Formatter::roundTo($metric);
    }

    /**
     * Returns array with tresholds for metric
     * @param  string $metric [description]
     * @return array         [description]
     */
    public function tresholdFilter($metric = null)
    {
        return Formatter::treshold($metric);
    }
}
