<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Jenssegers\Optimus\Optimus;

class OptimusExtension extends AbstractExtension
{
    /**
     * Autowire optimus
     * @param Optimus $optimus
     */
    public function __construct(Optimus $optimus)
    {
        $this->optimus = $optimus;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('optimus', array($this, 'optimusFilter')),
        );
    }

    /**
     * Returns number encoded by Optimus
     * @param  string $number [description]
     * @return string         [description]
     */
    public function optimusFilter($number)
    {
        return $this->optimus->encode($number);
    }
}
