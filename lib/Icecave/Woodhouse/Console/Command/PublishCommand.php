<?php
namespace Icecave\Woodhouse\Console\Command;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends Command
{
    public function __construct() {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        parent::__construct();
    }

    protected function configure()
    {
        $this->typeCheck->configure(func_get_args());

        $this->setName('publish');
        $this->setDescription('Publish content to a GitHub pages branch.');

        $this->addArgument(
            'content',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Pairs of source/destination folders separated by hyphens for publication (eg: stuff/source-folder:stuff/destination-folder).'
        );

        $this->addOption(
            'coverage-image',
            null,
            InputOption::VALUE_REQUIRED,
            'Publish a coverage badge to the given location (used in conjunction with one of the other --coverage-* options).'
        );

        $this->addOption(
            'coverage-phpunit',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to a PHPUnit code coverage report in text format.'
        );

        $this->addOption(
            'coverage-percentage',
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the coverage percentage directly on the command-line.'
        );

        $this->addOption(
            'auth-token',
            null,
            InputOption::VALUE_REQUIRED,
            'Use a GitHub OAuth API token for authentication.'
        );

        $this->addOption(
            'auth-token-env',
            null,
            InputOption::VALUE_REQUIRED,
            'Use a GitHub OAuth API token for authentication, the value should be the name of an environment variable containing the token.'
        );

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($input);

        $output->writeln('No-op.');
    }

    private $typeCheck;
}
