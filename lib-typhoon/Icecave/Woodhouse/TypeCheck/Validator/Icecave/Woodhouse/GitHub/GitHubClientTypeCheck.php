<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse\GitHub;

class GitHubClientTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
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
                    'url',
                    0,
                    $arguments[0],
                    'string|null'
                );
            }
        }
    }

    public function url(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function browser(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function authorizations(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function authorizationsMatching(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        if ($argumentCount > 0) {
            $value = $arguments[0];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'namePattern',
                    0,
                    $arguments[0],
                    'string|null'
                );
            }
        }
        if ($argumentCount > 1) {
            $value = $arguments[1];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'urlPattern',
                    1,
                    $arguments[1],
                    'string|null'
                );
            }
        }
    }

    public function createAuthorization(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 3) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        if ($argumentCount > 0) {
            $value = $arguments[0];
            $check = function ($value) {
                if (!\is_array($value)) {
                    return false;
                }
                foreach ($value as $key => $subValue) {
                    if (!\is_string($subValue)) {
                        return false;
                    }
                }
                return true;
            };
            if (!$check($arguments[0])) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'scopes',
                    0,
                    $arguments[0],
                    'array<string>'
                );
            }
        }
        if ($argumentCount > 1) {
            $value = $arguments[1];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'note',
                    1,
                    $arguments[1],
                    'string|null'
                );
            }
        }
        if ($argumentCount > 2) {
            $value = $arguments[2];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'noteUrl',
                    2,
                    $arguments[2],
                    'string|null'
                );
            }
        }
    }

    public function deleteAuthorization(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('id', 0, 'integer');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_int($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'id',
                0,
                $arguments[0],
                'integer'
            );
        }
    }

    public function get(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('url', 0, 'string');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'url',
                0,
                $arguments[0],
                'string'
            );
        }
    }

    public function post(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('url', 0, 'string');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('data', 1, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'url',
                0,
                $arguments[0],
                'string'
            );
        }
    }

    public function delete(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('url', 0, 'string');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'url',
                0,
                $arguments[0],
                'string'
            );
        }
    }

    public function parseJson(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('json', 0, 'string');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!\is_string($value)) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'json',
                0,
                $arguments[0],
                'string'
            );
        }
    }

}
