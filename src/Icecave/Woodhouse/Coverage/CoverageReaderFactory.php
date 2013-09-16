<?php
namespace Icecave\Woodhouse\Coverage;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\Coverage\Readers\CommandLineReader;
use Icecave\Woodhouse\Coverage\Readers\PhpUnitTextReader;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;

class CoverageReaderFactory
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
        $this->typeCheck->create(func_get_args());

        switch ($type) {
            case 'percentage':
                return new CommandLineReader($argument);
            case 'phpunit':
                return new PhpUnitTextReader($argument, $this->isolator);
        }

        throw new InvalidArgumentException('Unknown reader type: "' . $type . '".');
    }

    private $typeCheck;
    private $isolator;
}
