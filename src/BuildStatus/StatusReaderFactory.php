<?php

namespace Icecave\Woodhouse\BuildStatus;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\Readers\CommandLineReader;
use Icecave\Woodhouse\BuildStatus\Readers\JUnitReader;
use Icecave\Woodhouse\BuildStatus\Readers\PhpUnitJsonReader;
use Icecave\Woodhouse\BuildStatus\Readers\TapReader;
use InvalidArgumentException;

class StatusReaderFactory
{
    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return array<string>
     */
    public function supportedTypes()
    {
        return array(
            'junit',
            'phpunit',
            'result',
            'tap',
        );
    }

    /**
     * @param string $type
     * @param string $argument
     *
     * @return StatusReaderInterface
     */
    public function create($type, $argument)
    {
        switch ($type) {
            case 'junit':
                return new JUnitReader($argument, $this->isolator);
            case 'phpunit':
                return new PhpUnitJsonReader($argument, null, $this->isolator);
            case 'result':
                return new CommandLineReader($argument);
            case 'tap':
                return new TapReader($argument, $this->isolator);
        }

        throw new InvalidArgumentException('Unknown reader type: "' . $type . '".');
    }

    private $isolator;
}
