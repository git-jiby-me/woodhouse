<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
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

        $fp = $this->isolator->fopen($this->reportPath, 'r');

        $buffer = '';

        // THe PHPUnit JSON output is not actually valid JSON.
        // It contains sequential JSON objects, so we read the
        // file line by line looking for the termination of those
        // objects before dispatching to a standard json_decode call.
        while ($line = $this->isolator->fgets($fp)) {

            // The object ended ...
            if ('}' === $line[0]) {
                $buffer .= '}';

                if ($buildStatus = $this->parse($buffer)) {
                    return $buildStatus;
                }

                $buffer = substr($line, 1) ?: '';

            // Midway through an object ...
            } elseif (false !== $line) {
                $buffer .= $line;
            }
        }

        // There was some un-ended object in the buffer ...
        if ('' !== $buffer) {
            throw new RuntimeException('Unable to parse PHPUnit test report.');
        }

        return BuildStatus::PASSING();
    }

    /**
     * @param string $buffer
     */
    protected function parse($buffer)
    {
        $this->typeCheck->parse(func_get_args());

        $object = json_decode($buffer);

        if (isset($object->status) && $object->status !== "pass") {
            return BuildStatus::FAILING();
        }

        return null;
    }

    private $typeCheck;
    private $reportPath;
    private $isolator;
}
