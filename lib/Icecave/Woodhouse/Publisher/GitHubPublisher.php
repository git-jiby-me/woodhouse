<?php
namespace Icecave\Woodhouse\Publisher;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;
use RuntimeException;

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
        $this->commitMessage = 'Content published by Woodhouse.';
        $this->maxPushAttempts = 3;
    }

    /**
     * Publish enqueued content.
     */
    public function publish()
    {
        $tempDir = $this->isolator->sys_get_temp_dir() . '/woodhouse-' . $this->isolator->getmypid();

        // Clone the Git repository ...
        $output = $this->execute(
            'git', 'clone',
            '--branch', $this->branch(),
            '--depth', 0,
            $this->repositoryUrl(),
            $tempDir
        );
        
        // Create the brach if it doesn't exist ...
        if (false !== strpos($output, $this->branch() . ' not found in upstream origin')) {
            $this->execute('git', 'checkout', '--orphan', $this->branch());
            $this->execute('cd', $tempDir);
            $this->execute('git', 'rm', '-rf', '.');
        
        // Branch does exist ...
        } else {
            $this->execute('cd', $tempDir);
        }

        // Remove existing content that exists in target paths ...
        foreach ($this->contentPaths() as $sourcePath => $targetPath) {
            $this->execute('git', 'rm', '-rf', $targetPath);
        }

        // Copy in published content and add it to the repo ...
        foreach ($this->contentPaths() as $sourcePath => $targetPath) {
            $this->execute('cp', '-r', $sourcePath, $targetPath);
            $this->execute('git', 'add', $targetPath);
        }

        ////////
        return ;
        ////////

        // Commit the published content ...
        $this->execute('git', 'commit', '-m', $this->commitMessage());

        // Make push attempts ...
        for ($attempt = 1; $attempt <= $this->maxPushAttempts; ++$attempt) {
            if ($this->tryExecute('git', 'push', 'origin', $this->branch())) {
                return;
            }
            $this->execute('git', 'pull');
        }
        
        throw new RuntimeException('Unable to publish content.');
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
     * @return string
     */
    public function commitMessage()
    {
        $this->typeCheck->commitMessage(func_get_args());

        return $this->commitMessage;
    }

    /**
     * @param string $commitMessage
     */
    public function setCommitMessage($commitMessage)
    {
        $this->typeCheck->setCommitMessage(func_get_args());

        $this->commitMessage = $commitMessage;
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

    /**
     * @param string $command
     * @param string $argument,...
     */
    protected function execute($command) {
        $this->typeCheck->execute(func_get_args());

        $arguments = array_slice(func_get_args(), 1);
        if (!$this->tryExecuteArray($command, $arguments)) {
            throw new RuntimeException('Failed executing command: "' . $command . '".');
        }
    }

    /**
     * @param string $command
     * @param string $argument,...
     */
    protected function tryExecute($command)
    {
        $this->typeCheck->tryExecute(func_get_args());

        $arguments = array_slice(func_get_args(), 1);
        return $this->tryExecuteArray($command, $arguments);
    }

    /**
     * @param string $command
     * @param array<string> $arguments
     */
    protected function tryExecuteArray($command, array $arguments)
    {
        $this->typeCheck->tryExecuteArray(func_get_args());

        $commandLine = '/usr/bin/env ' . escapeshellarg($command);
        foreach ($arguments as $arg) {
            $commandLine .= ' ' . escapeshellarg($arg);
        }

        $exitCode = null;
        $this->isolator->exec($commandLine, $output, $exitCode);

        if (0 === $exitCode) {
            return implode(PHP_EOL, $ouptut);
        }

        return null;
    }

    private $typeCheck;
    private $repository;
    private $branch;
    private $commitMessage;
    private $authToken;
    private $maxPushAttempts;
}
