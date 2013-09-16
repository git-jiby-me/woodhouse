<?php
namespace Icecave\Woodhouse\TypeCheck\Validator\Icecave\Woodhouse\Console\Command\GitHub;

class AbstractGitHubCommandTypeCheck extends \Icecave\Woodhouse\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function clientFactory(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function setApplication(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 1) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function configure(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function createClient(array $arguments)
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

    public function credentials(array $arguments)
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

    public function outputAuthorizations(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 0, 'Symfony\\Component\\Console\\Output\\OutputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('authorizations', 1, 'array<stdClass>');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[1];
        $check = function ($value) {
            if (!\is_array($value)) {
                return false;
            }
            foreach ($value as $key => $subValue) {
                if (!$subValue instanceof \stdClass) {
                    return false;
                }
            }
            return true;
        };
        if (!$check($arguments[1])) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentValueException(
                'authorizations',
                1,
                $arguments[1],
                'array<stdClass>'
            );
        }
    }

    public function outputAuthorization(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('output', 0, 'Symfony\\Component\\Console\\Output\\OutputInterface');
            }
            throw new \Icecave\Woodhouse\TypeCheck\Exception\MissingArgumentException('authorization', 1, 'stdClass');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Woodhouse\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
    }

}
