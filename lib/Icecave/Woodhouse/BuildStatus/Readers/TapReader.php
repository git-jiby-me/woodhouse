<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use RuntimeException;

class TapReader implements StatusReaderInterface
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

        $content = $this->isolator->file_get_contents($this->reportPath);

        if (preg_match('/^not ok/m', $content)) {
            return BuildStatus::FAILING();
        } elseif (preg_match('/^ok/m', $content)) {
            return BuildStatus::PASSING();
        }

        throw new RuntimeException('Unable to parse TAP test report.');
    }

    private $typeCheck;
    private $reportPath;
    private $isolator;
}
