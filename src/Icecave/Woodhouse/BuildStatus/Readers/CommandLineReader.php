<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use Icecave\Woodhouse\TypeCheck\TypeCheck;

class CommandLineReader implements StatusReaderInterface
{
    /**
     * @param string $buildStatus
     */
    public function __construct($buildStatus)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->buildStatus = $buildStatus;
    }

    /**
     * @return BuildStatus
     */
    public function readStatus()
    {
        $this->typeCheck->readStatus(func_get_args());

        return BuildStatus::instanceByValue($this->buildStatus);
    }

    private $typeCheck;
    private $buildStatus;
}
