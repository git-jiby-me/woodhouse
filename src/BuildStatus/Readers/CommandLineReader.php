<?php

namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;

class CommandLineReader implements StatusReaderInterface
{
    /**
     * @param string $buildStatus
     */
    public function __construct($buildStatus)
    {
        $this->buildStatus = $buildStatus;
    }

    /**
     * @return BuildStatus
     */
    public function readStatus()
    {
        return BuildStatus::memberByValue($this->buildStatus);
    }

    private $buildStatus;
}
