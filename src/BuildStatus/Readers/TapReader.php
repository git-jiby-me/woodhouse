<?php

namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use RuntimeException;

class TapReader implements StatusReaderInterface
{
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
     * @return BuildStatus
     */
    public function readStatus()
    {
        $content = $this->isolator->file_get_contents($this->reportPath);

        if (preg_match('/^not ok/m', $content)) {
            return BuildStatus::FAILING();
        } elseif (preg_match('/^ok/m', $content)) {
            return BuildStatus::PASSING();
        }

        throw new RuntimeException('Unable to parse TAP test report.');
    }

    private $reportPath;
    private $isolator;
}
