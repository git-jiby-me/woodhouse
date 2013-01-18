<?php
namespace Icecave\Woodhouse\Coverage;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Icecave\Woodhouse\Coverage\Readers\PHPUnitTextReader;
use Icecave\Woodhouse\Coverage\Readers\CommandLineReader;

interface CoverageReaderInterface
{
    /**
     * @return float
     */
    public function readPercentage();
}
