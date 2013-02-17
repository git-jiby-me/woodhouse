<?php
namespace Icecave\Woodhouse\Git;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Git
{
    /**
     * @param ExecutableFinder|null $executableFinder
     */
    public function __construct(
        ExecutableFinder $executableFinder = null
    ) {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $executableFinder) {
            $executableFinder = new ExecutableFinder;
        }

        $this->executable = $executableFinder->find('git');
        $this->outputFilter = function ($output) { return $output; };
    }

    /**
     * @param callable|null $callback
     */
    public function setOutputFilter($callback)
    {
        $this->typeCheck->setOutputFilter(func_get_args());

        $this->outputFilter = $callback;
    }

    /**
     * @param string       $path
     * @param string       $url
     * @param string       $branch
     * @param integer|null $depth
     *
     * @return Process
     */
    public function cloneRepo($path, $url, $branch = 'master', $depth = null)
    {
        $this->typeCheck->cloneRepo(func_get_args());

        $arguments = array('clone', $url, '--branch', $branch);
        if (null !== $depth) {
            $arguments[] = '--depth';
            $arguments[] = $depth;
        }

        $arguments[] = $path;

        $process = $this->execute($arguments);

        $this->workingDirectory = $path;

        return $process;
    }

    /**
     * @param string  $branch
     * @param boolean $orphan
     *
     * @return Process
     */
    public function checkout($branch, $orphan = false)
    {
        $this->typeCheck->checkout(func_get_args());

        $arguments = array('checkout');
        if ($orphan) {
            $arguments[] = '--orphan';
        }
        $arguments[] = $branch;

        return $this->execute($arguments);
    }

    /**
     * @param string  $path
     * @param boolean $force
     *
     * @return Process
     */
    public function add($path, $force = true)
    {
        $this->typeCheck->add(func_get_args());

        $arguments = array('add', $path);

        return $this->execute($arguments);
    }

    /**
     * @param string  $path
     * @param boolean $force
     *
     * @return Process
     */
    public function remove($path, $force = true)
    {
        $this->typeCheck->remove(func_get_args());

        $arguments = array('rm');
        if ($force) {
            $arguments[] = '-rf';
            $arguments[] = '--ignore-unmatch';
        }
        $arguments[] = $path;

        return $this->execute($arguments);
    }

    /**
     * @param boolean $diffStagedFiles
     *
     * @return Process
     */
    public function diff($diffStagedFiles = false)
    {
        $this->typeCheck->diff(func_get_args());

        $arguments = array('diff');
        if ($diffStagedFiles) {
            $arguments[] = '--cached';
        }

        return $this->execute($arguments);
    }

    /**
     * @param string $message
     *
     * @return Process
     */
    public function commit($message)
    {
        $this->typeCheck->commit(func_get_args());

        $arguments = array('commit', '-m', $message);

        return $this->execute($arguments);
    }

    /**
     * @param string      $remote
     * @param string|null $branch
     *
     * @return Process
     */
    public function push($remote = 'origin', $branch = null)
    {
        $this->typeCheck->push(func_get_args());

        $arguments = array('push', $remote);
        if (null !== $branch) {
            $arguments[] = $branch;
        }

        return $this->execute($arguments);
    }

    /**
     * @return Process
     */
    public function pull()
    {
        $this->typeCheck->pull(func_get_args());

        $arguments = array('pull');

        return $this->execute($arguments);
    }

    /**
     * @param string $key
     * @param stringable $value
     *
     * @return Process
     */
    public function setConfig($key, $value)
    {
        $this->typeCheck->setConfig(func_get_args());

        $arguments = array('config', $key, $value);

        return $this->execute($arguments);
    }

    /**
     * @return string
     */
    public function executable()
    {
        $this->typeCheck->executable(func_get_args());

        return $this->executable;
    }

    /**
     * @param array<stringable> $arguments
     *
     * @return Process
     */
    public function execute(array $arguments)
    {
        $this->typeCheck->execute(func_get_args());

        $process = $this->createProcess();

        $command = $this->executable;
        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $process->setCommandLine($command);

        if ($this->workingDirectory) {
            $process->setWorkingDirectory($this->workingDirectory);
        }

        $process->run();

        if (!$process->isSuccessful()) {
            $message  = 'Git command failed!';
            $message .= PHP_EOL;
            $message .= 'Command Line: ' . call_user_func($this->outputFilter, $command);
            $message .= PHP_EOL;
            $message .= PHP_EOL;
            $message .= call_user_func($this->outputFilter, $process->getErrorOutput());
            throw new RuntimeException($message);
        }

        return $process;
    }

    /**
     * @return Process
     */
    protected function createProcess()
    {
        $this->typeCheck->createProcess(func_get_args());

        return new Process('');
    }

    private $typeCheck;
    private $outputFilter;
    private $executable;
    private $workingDirectory;
}
