<?php

namespace Icecave\Woodhouse\Coverage\Readers;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\Coverage\CoverageReaderInterface;
use RuntimeException;

class PhpUnitTextReader implements CoverageReaderInterface
{
    const PATTERN = '{^\s+Lines:.+\((\d+)/(\d+)\)$}m';

    /**
     * @param string        $reportPath
     * @param Isolator|null $isolator
     */
    public function __construct($reportPath, Isolator $isolator = null)
    {
        $this->reportPath = $reportPath;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return float
     */
    public function readPercentage()
    {
        $content = $this->isolator->file_get_contents($this->reportPath);

        $matches = array();
        if (preg_match(self::PATTERN, $content, $matches)) {
            if ($matches[1] === $matches[2]) {
                return 100.00; // Handle 0/0
            }

            return round(100.00 * ($matches[1] / $matches[2]), 2);
        }

        throw new RuntimeException('Unable to parse PHPUnit coverage report.');
    }

    private $reportPath;
    private $isolator;
}
