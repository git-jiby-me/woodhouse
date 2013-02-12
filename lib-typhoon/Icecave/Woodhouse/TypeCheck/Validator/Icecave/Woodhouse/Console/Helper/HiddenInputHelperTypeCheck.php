<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse\Console\Helper;

class HiddenInputHelperTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        if ($argumentCount > 0) {
            $value = $arguments[0];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'hiddenInputPath',
                    0,
                    $arguments[0],
                    'string|null'
                );
            }
        }
    }

    public function getName(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function hiddenInputPath(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function askHiddenResponse(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 0, 'Symfony\\Component\\Console\\Output\\OutputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('question', 1, 'string|array');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[1];
        if (!(\is_string($value) || \is_array($value))) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'question',
                1,
                $arguments[1],
                'string|array'
            );
        }
    }

    public function askHiddenResponseWindows(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 0, 'Symfony\\Component\\Console\\Output\\OutputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('question', 1, 'string|array');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[1];
        if (!(\is_string($value) || \is_array($value))) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'question',
                1,
                $arguments[1],
                'string|array'
            );
        }
    }

    public function askHiddenResponseStty(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 0, 'Symfony\\Component\\Console\\Output\\OutputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('question', 1, 'string|array');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[1];
        if (!(\is_string($value) || \is_array($value))) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'question',
                1,
                $arguments[1],
                'string|array'
            );
        }
    }

    public function hiddenInputRealPath(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function execute(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('command', 0, 'string');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'command',
                0,
                $arguments[0],
                'string'
            );
        }
    }

}
