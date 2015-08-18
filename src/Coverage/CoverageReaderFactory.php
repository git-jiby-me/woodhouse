<?php

namespace Icecave\Woodhouse\Coverage;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\Coverage\Readers\CommandLineReader;
use Icecave\Woodhouse\Coverage\Readers\PhpUnitTextReader;
use InvalidArgumentException;

class CoverageReaderFactory
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
            'percentage',
            'phpunit',
        );
    }

    /**
     * @param string $type
     * @param string $argument
     *
     * @return CoverageReaderInterface
     */
    public function create($type, $argument)
    {
        switch ($type) {
            case 'percentage':
                return new CommandLineReader($argument);
            case 'phpunit':
                return new PhpUnitTextReader($argument, $this->isolator);
        }

        throw new InvalidArgumentException('Unknown reader type: "' . $type . '".');
    }

    private $isolator;
}
