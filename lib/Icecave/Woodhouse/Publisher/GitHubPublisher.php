<?php
namespace Icecave\Woodhouse\Publisher;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;

class GitHubPublisher extends AbstractPublisher
{
    const REPOSITORY_PATTERN = '/^[a-z0-9_-]+\/[a-z0-9_-]+$/i';
    const AUTH_TOKEN_PATTERN = '/^[0-9a-f]{40}$/i';

    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->isolator = Isolator::get($isolator);
        $this->branch = 'gh-pages';
    }

    /**
     * Publish enqueued content.
     *
     * @param callable $outputCallback
     */
    public function publish($outputCallback)
    {
        throw new \Exception('Not implemented!');
    }

    /**
     * @return string
     */
    public function repository()
    {
        $this->typeCheck->repository(func_get_args());

        return $this->repository;
    }

    /**
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->typeCheck->setRepository(func_get_args());

        if (!preg_match(self::REPOSITORY_PATTERN, $repository)) {
            throw new InvalidArgumentException('Invalid repository name: "' . $repository . '".');
        }

        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function branch()
    {
        $this->typeCheck->branch(func_get_args());

        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->typeCheck->setBranch(func_get_args());

        $this->branch = $branch;
    }
    /**
     * @return string|null
     */
    public function authToken()
    {
        $this->typeCheck->authToken(func_get_args());

        return $this->authToken;

    }

    /**
     * @param string|null $authToken
     */
    public function setAuthToken($authToken)
    {
        $this->typeCheck->setAuthToken(func_get_args());

        if (!preg_match(self::AUTH_TOKEN_PATTERN, $authToken)) {
            // Note that the provided token is deliberately not included in the exception
            // message to prevent possible leaks of strings that are very-near to a real token.
            throw new InvalidArgumentException('Invalid authentication token.');
        }

        $this->authToken = strtolower($authToken);
    }

    private $typeCheck;
    private $repository;
    private $branch;
    private $authToken;
}
