<?php
namespace Icecave\Woodhouse\BuildStatus;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\Readers\JUnitReader;
use Icecave\Woodhouse\BuildStatus\Readers\TapReader;
use Icecave\Woodhouse\BuildStatus\Readers\PhpUnitJsonReader;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;

class StatusReaderFactory
{
    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return array<string>
     */
    public function supportedTypes()
    {
        $this->typeCheck->supportedTypes(func_get_args());

        return array(
            'junit',
            'phpunit',
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
        $this->typeCheck->create(func_get_args());

        switch ($type) {
            case 'junit':
                return new JUnitReader($argument, $this->isolator);
            case 'phpunit':
                return new PhpUnitJsonReader($argument, $this->isolator);
            case 'tap':
                return new TapReader($argument, $this->isolator);
        }

        throw new InvalidArgumentException('Unknown reader type: "' . $type . '".');
    }

    private $typeCheck;
    private $isolator;
}
