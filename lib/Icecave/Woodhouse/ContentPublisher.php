<?php
namespace Icecave\Woodhouse;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;
use Icecave\Isolator\Isolator;

class ContentPublisher
{
    const AUTH_TOKEN_PATTERN = '/^[0-9a-f]{40}$/i';

    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());
        
        $this->contentPaths = array();
        $this->authToken = null;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @param string $sourcePath
     * @param string $targetPath
     */
    public function add($sourcePath, $targetPath)
    {
        $this->typeCheck->add(func_get_args());

        $this->contentPaths[$sourcePath] = $targetPath;
    }

    /**
     * @param string $sourcePath
     */
    public function remove($sourcePath)
    {
        $this->typeCheck->remove(func_get_args());
        
        unset($this->contentPaths[$sourcePath]);
    }

    public function clear()
    {
        $this->typeCheck->clear(func_get_args());

        $this->contentPaths = array();
    }

    /**
     * @param string $repository
     * @param string $branch
     * @param callable $outputCallback
     */
    public function publish($repository, $branch = 'gh-pages', $outputCallback)
    {
        throw new \Exception('Not implemented!');
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
            throw new InvalidArgumentException('The provided authentication token is not valid.');
        }

        $this->authToken = strtolower($authToken);
    }

    private $typeCheck;
    private $contentPaths;
    private $authToken;
}
