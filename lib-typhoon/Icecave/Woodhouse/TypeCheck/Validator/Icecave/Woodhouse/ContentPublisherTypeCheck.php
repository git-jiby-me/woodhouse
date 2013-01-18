<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse;

class ContentPublisherTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function add(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('sourcePath', 0, 'string');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('targetPath', 1, 'string');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'sourcePath',
                0,
                $arguments[0],
                'string'
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
    }

    public function remove(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('sourcePath', 0, 'string');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'sourcePath',
                0,
                $arguments[0],
                'string'
            );
        }
    }

    public function clear(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function publish(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('repository', 0, 'string');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('branch', 1, 'string');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('outputCallback', 2, 'callable');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'repository',
                0,
                $arguments[0],
                'string'
            );
        }
        $value = $arguments[1];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'branch',
                1,
                $arguments[1],
                'string'
            );
        }
        $value = $arguments[2];
        if (!\is_callable($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'outputCallback',
                2,
                $arguments[2],
                'callable'
            );
        }
    }

    public function authToken(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function setAuthToken(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('authToken', 0, 'string|null');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_string($value) || $value === null)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'authToken',
                0,
                $arguments[0],
                'string|null'
            );
        }
    }

}
