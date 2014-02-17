<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Exception;
use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use RuntimeException;

class JUnitReader implements StatusReaderInterface
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
        try {
            $xml = $this->isolator->simplexml_load_file($this->reportPath);
        } catch (Exception $e) {
            throw new RuntimeException('Unable to parse JUnit test report.', 0, $e);
        }

        foreach ($xml as $suite) {
            if ($suite['failures'] > 0 || $suite['errors'] > 0) {
                return BuildStatus::FAILING();
            }
        }

        return BuildStatus::PASSING();
    }

    private $reportPath;
    private $isolator;
}
