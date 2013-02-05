<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse\GitHub;

class GitHubClientFactoryTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
{
    public function create(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        if ($argumentCount > 0) {
            $value = $arguments[0];
            if (!(\is_string($value) || $value === null)) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'username',
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
                    'password',
                    1,
                    $arguments[1],
                    'string|null'
                );
            }
        }
    }

}
