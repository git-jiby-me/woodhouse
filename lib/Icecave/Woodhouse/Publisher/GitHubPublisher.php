<?php
namespace Icecave\Woodhouse\Publisher;

use Exception;
use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\Git\Git;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class GitHubPublisher extends AbstractPublisher
{
    const REPOSITORY_PATTERN = '/^[a-z0-9_-]+\/[a-z0-9_-]+$/i';
    const AUTH_TOKEN_PATTERN = '/^[0-9a-f]{40}$/i';

    /**
     * @param Git|null        $git
     * @param Filesystem|null $fileSystem
     * @param Isolator|null   $isolator
     */
    public function __construct(
        Git $git = null,
        Filesystem $fileSystem = null,
        Isolator $isolator = null
    ) {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $git) {
            $git = new Git;
        }

        if (null === $fileSystem) {
            $fileSystem = new Filesystem;
        }

        $this->git = $git;
        $this->branch = 'gh-pages';
        $this->commitMessage = 'Content published by Woodhouse.';
        $this->maxPushAttempts = 3;
        $this->fileSystem = $fileSystem;
        $this->isolator = Isolator::get($isolator);

        // Setup an output filter so that the auth token cannot be leaked ...
        $self = $this;
        $filter = function ($buffer) use ($self) {
            $authToken   = $self->authToken();
            $replacement = substr($authToken, 0, 4) . str_repeat('*', strlen($authToken) - 8) . substr($authToken, -4);

            return str_ireplace($authToken, $replacement, $buffer);
        };

        $this->git->setOutputFilter($filter);

        parent::__construct();
    }

    /**
     * @return Filesystem
     */
    public function fileSystem()
    {
        $this->typeCheck->fileSystem(func_get_args());

        return $this->fileSystem;
    }

    /**
     * @return Git
     */
    public function git()
    {
        $this->typeCheck->git(func_get_args());

        return $this->git;
    }

    /**
     * Publish enqueued content.
     *
     * @return boolean True if there were changes published; otherwise false.
     */
    public function publish()
    {
        $this->typeCheck->publish(func_get_args());

        return $this->doPublish(true);
    }

    /**
     * Perform a publication dry-run.
     *
     * @return boolean True if there are changes to publish; otherwise false.
     */
    public function dryRun()
    {
        $this->typeCheck->dryRun(func_get_args());

        return $this->doPublish(false);
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
    public function repositoryUrl()
    {
        $this->typeCheck->repositoryUrl(func_get_args());

        if (null === $this->repository) {
            return null;
        } elseif (null === $this->authToken) {
            return sprintf('https://github.com/%s.git', $this->repository);
        } else {
            return sprintf('https://%s:x-oauth-basic@github.com/%s.git', $this->authToken, $this->repository);
        }
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
     * Publish enqueued content.
     *
     * @param boolean $commit
     *
     * @return boolean True if there were changes published; otherwise false.
     */
    protected function doPublish($commit)
    {
        $this->typeCheck->doPublish(func_get_args());

        if (null === $this->repository) {
            throw new RuntimeException('No repository set.');
        }

        $tempDir = $this->isolator->sys_get_temp_dir() . '/woodhouse-' . $this->isolator->getmypid();

        try {
            $this->cloneRepo($tempDir);
            $this->stageContent($tempDir);

            // Check if there are any changes ...
            $process = $this->git->diff(true);
            if (trim($process->getOutput()) === '') {
                return false;
            }

            if ($commit) {
                $this->git->setConfig('user.name', 'Woodhouse');
                $this->git->setConfig('user.email', 'contact@icecave.com.au');
                $this->git->commit($this->commitMessage());

                $this->push();
            }

            $this->fileSystem->remove($tempDir);

            return true;

        } catch (Exception $e) {
            $this->fileSystem->remove($tempDir);
            throw $e;
        }
    }

     /**
      * @param string $tempDir
      */
    protected function cloneRepo($tempDir)
    {
         $this->typeCheck->cloneRepo(func_get_args());

         try {
             $this->git->cloneRepo($tempDir, $this->repositoryUrl(), $this->branch(), 0);
             foreach ($this->contentPaths() as $sourcePath => $targetPath) {
                 $this->git->remove($targetPath);
             }
         } catch (RuntimeException $e) {
             if (false === strpos($e->getMessage(), $this->branch() . ' not found in upstream origin')) {
                 throw $e;
             }

             $this->git->cloneRepo($tempDir, $this->repositoryUrl(), null, 0);
             $this->git->checkout($this->branch(), true);
             $this->git->remove('.');
         }
    }

     /**
      * @param string $tempDir
      */
     protected function stageContent($tempDir)
     {
         $this->typeCheck->stageContent(func_get_args());

         $this->isolator->chdir($tempDir);

         foreach ($this->contentPaths() as $sourcePath => $targetPath) {
             $fullTargetPath = $tempDir . '/' . $targetPath;
             $fullTargetParentPath = dirname($fullTargetPath);

             if (!$this->isolator->is_dir($fullTargetParentPath)) {
                 $this->isolator->mkdir($fullTargetParentPath, 0777, true);
             }

             if ($this->isolator->is_dir($sourcePath)) {
                 $sourcePath = rtrim($sourcePath, '/') . '/';
                 $this->fileSystem->mirror($sourcePath, $fullTargetPath);
             } else {
                 $this->fileSystem->copy($sourcePath, $fullTargetPath);
             }

             $this->git->add($targetPath);
         }
     }

     /**
      * @return Process
      */
     protected function push()
     {
         $this->typeCheck->push(func_get_args());

         // Supress exceptions for $max-1 attempts ...
         $attemptsRemaining = $this->maxPushAttempts;

         while (--$attemptsRemaining) {
             try {
                 return $this->git->push('origin', $this->branch());
             } catch (RuntimeException $e) {
                 $this->git->pull();
             }
         }

         // Final attempt, allow exceptions to propagate ...
         return $this->git->push('origin', $this->branch());
     }

    private $typeCheck;
    private $git;
    private $repository;
    private $branch;
    private $commitMessage;
    private $authToken;
    private $maxPushAttempts;
    private $fileSystem;
    private $isolator;
}
