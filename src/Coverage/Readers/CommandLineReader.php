<?php

namespace Icecave\Woodhouse\Coverage\Readers;

use Icecave\Woodhouse\Coverage\CoverageReaderInterface;

class CommandLineReader implements CoverageReaderInterface
{
    /**
     * @param numeric $percentage
     */
    public function __construct($percentage)
    {
        $this->percentage = floatval($percentage);
    }

    /**
     * @return float
     */
    public function readPercentage()
    {
        return $this->percentage;
    }

    private $percentage;
}
