<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Twig;

use Jenssegers\Optimus\Optimus;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class OptimusExtension extends AbstractExtension
{
    protected $optimus;

    /**
     * Autowire optimus.
     */
    public function __construct(Optimus $optimus)
    {
        $this->optimus = $optimus;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('optimus', [$this, 'optimusFilter']),
        ];
    }

    /**
     * Returns number encoded by Optimus.
     *
     * @param string $number [description]
     *
     * @return string [description]
     */
    public function optimusFilter($number)
    {
        return $this->optimus->encode($number);
    }
}
