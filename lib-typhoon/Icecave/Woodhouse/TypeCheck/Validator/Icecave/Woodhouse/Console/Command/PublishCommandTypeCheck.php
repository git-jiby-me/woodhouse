<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse\Console\Command;

class PublishCommandTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 6) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(6, $arguments[6]);
        }
    }

    public function configure(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function resolveReader(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('factory', 0, 'mixed');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('optionPrefix', 1, 'string');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('input', 2, 'Symfony\\Component\\Console\\Input\\InputInterface');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[1];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'optionPrefix',
                1,
                $arguments[1],
                'string'
            );
        }
    }

    public function resolveThemes(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('input', 0, 'Symfony\\Component\\Console\\Input\\InputInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function enqueueImages(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 4) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('themes', 0, 'array<string, string>');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('targetPath', 1, 'string');
            }
            if ($argumentCount < 3) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('category', 2, 'string');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('filename', 3, 'string');
        } elseif ($argumentCount > 4) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(4, $arguments[4]);
        }
        $value = $arguments[0];
        $check = function ($value) {
            if (!\is_array($value)) {
                return false;
            }
            foreach ($value as $key => $subValue) {
                if (!\is_string($key)) {
                    return false;
                }
                if (!\is_string($subValue)) {
                    return false;
                }
            }
            return true;
        };
        if (!$check($arguments[0])) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'themes',
                0,
                $arguments[0],
                'array<string, string>'
            );
        }
        $value = $arguments[1];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'targetPath',
                1,
                $arguments[1],
                'string'
            );
        }
        $value = $arguments[2];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'category',
                2,
                $arguments[2],
                'string'
            );
        }
        $value = $arguments[3];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'filename',
                3,
                $arguments[3],
                'string'
            );
        }
    }

    public function execute(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('input', 0, 'Symfony\\Component\\Console\\Input\\InputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 1, 'Symfony\\Component\\Console\\Output\\OutputInterface');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
    }

}
