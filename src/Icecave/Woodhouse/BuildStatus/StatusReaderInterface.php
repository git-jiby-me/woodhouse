<?php
namespace Icecave\Woodhouse\BuildStatus;

interface StatusReaderInterface
{
    /**
     * @return BuildStatus
     */
    public function readStatus();
}
