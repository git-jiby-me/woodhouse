<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Collections\Vector;
use Icecave\Duct\Exception\SyntaxExceptionInterface;
use Icecave\Duct\Parser;
use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusReaderInterface;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use RuntimeException;

class PhpUnitJsonReader implements StatusReaderInterface
{
    /**
     * @param string        $reportPath
     * @param Parser|null   $parser
     * @param Isolator|null $isolator
     */
    public function __construct($reportPath, Parser $parser = null, Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $parser) {
            $parser = new Parser;
        }

        $this->reportPath = $reportPath;
        $this->parser = $parser;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return BuildStatus
     */
    public function readStatus()
    {
        $this->typeCheck->readStatus(func_get_args());

        $fp = $this->isolator->fopen($this->reportPath, 'r');

        $this->parser->reset();

        try {
            while (!$this->isolator->feof($fp)) {
                $this->parser->feed(
                    $this->isolator->fread($fp, 1024)
                );

                if ($buildStatus = $this->checkResults($this->parser->values())) {
                    return $buildStatus;
                }
            }
            $this->parser->finalize();
        } catch (SyntaxExceptionInterface $e) {
            throw new RuntimeException('Unable to parse PHPUnit test report.', 0, $e);
        }

        return BuildStatus::PASSING();
    }

    /**
     * @param Vector $results
     *
     * @return BuildStatus|null
     */
    protected function checkResults(Vector $results)
    {
        $this->typeCheck->checkResults(func_get_args());

        foreach ($results as $result) {
            if (isset($result->status) && $result->status !== "pass") {
                return BuildStatus::FAILING();
            }
        }

        return null;
    }

    private $typeCheck;
    private $reportPath;
    private $parser;
    private $isolator;
}
