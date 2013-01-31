<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use RuntimeException;

class PhpUnitJsonReader implements StatusReaderInterface
{
    /**
     * @param string        $reportPath
     * @param Isolator|null $isolator
     */
    public function __construct($reportPath, Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->reportPath = $reportPath;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return BuildStatus
     */
    public function readStatus()
    {
        $this->typeCheck->readStatus(func_get_args());

        throw new RuntimeException('Unable to parse PHPUnit test report.');
    }

    private $typeCheck;
    private $reportPath;
    private $isolator;
}
