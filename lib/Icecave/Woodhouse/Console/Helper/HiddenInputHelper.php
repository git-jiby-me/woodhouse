<?php
namespace Icecave\Woodhouse\Console\Helper;

use ErrorException;
use Icecave\Isolator\Isolator;
use RuntimeException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;
use Icecave\Woodhouse\TypeCheck\TypeCheck;

class HiddenInputHelper extends Helper
{
    /**
     * @param string|null   $hiddenInputPath
     * @param Isolator|null $isolator
     */
    public function __construct($hiddenInputPath = null, Isolator $isolator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $hiddenInputPath) {
            $hiddenInputPath = __DIR__ . '/../../../../../res/bin/hiddeninput.exe';
        }

        $this->hiddenInputPath = $hiddenInputPath;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return string
     */
    public function getName()
    {
        $this->typeCheck->getName(func_get_args());

        return 'hidden-input';
    }

    /**
     * @return string
     */
    public function hiddenInputPath()
    {
        $this->typeCheck->hiddenInputPath(func_get_args());

        return $this->hiddenInputPath;
    }

    /**
     * @param OutputInterface $output
     * @param string|array    $question
     *
     * @return string
     */
    public function askHiddenResponse(OutputInterface $output, $question)
    {
        $this->typeCheck->askHiddenResponse(func_get_args());

        if ($this->isolator->defined('PHP_WINDOWS_VERSION_BUILD')) {
            return $this->askHiddenResponseWindows($output, $question);
        }

        return $this->askHiddenResponseStty($output, $question);
    }

    /**
     * @param OutputInterface $output
     * @param string|array    $question
     *
     * @return string
     */
    protected function askHiddenResponseWindows(OutputInterface $output, $question)
    {
        $this->typeCheck->askHiddenResponseWindows(func_get_args());

        $output->write($question);
        $value = rtrim(
            $this->execute($this->hiddenInputRealPath()),
            "\r\n"
        );
        $output->writeln('');

        return $value;
    }

    /**
     * @param OutputInterface $output
     * @param string|array    $question
     *
     * @return string
     */
    protected function askHiddenResponseStty(OutputInterface $output, $question)
    {
        $this->typeCheck->askHiddenResponseStty(func_get_args());

        $output->write($question);

        $sttyMode = $this->execute('stty -g');
        $this->execute('stty -echo');

        $error = null;

        try {
            $value = rtrim(
                $this->isolator->fgets(STDIN),
                "\r\n"
            );
        } catch (ErrorException $error) {
            // reset stty before throwing
        }

        $this->execute(sprintf('stty %s', $sttyMode));
        if (null !== $error) {
            throw new RuntimeException('Unable to read response.', 0, $error);
        }
        $output->writeln('');

        return $value;
    }

    /**
     * @return string
     */
    protected function hiddenInputRealPath()
    {
        $this->typeCheck->hiddenInputRealPath(func_get_args());

        if (null === $this->hiddenInputRealPath) {
            $this->hiddenInputRealPath = sprintf(
                '%s/hiddeninput-%s.exe',
                $this->isolator->sys_get_temp_dir(),
                $this->isolator->uniqid()
            );
            $this->isolator->copy(
                $this->hiddenInputPath(),
                $this->hiddenInputRealPath
            );
        }

        return $this->hiddenInputRealPath;
    }

    /**
     * @param string $command
     *
     * @return string
     */
    protected function execute($command)
    {
        $this->typeCheck->execute(func_get_args());

        $result = $this->isolator->shell_exec($command);
        if (false === $result) {
            throw new RuntimeException('Unable to create or read hidden input dialog.');
        }

        return $result;
    }

    private $hiddenInputPath;
    private $hiddenInputRealPath;
    private $isolator;
    private $hasSttyAvailable;
    private $typeCheck;
}
