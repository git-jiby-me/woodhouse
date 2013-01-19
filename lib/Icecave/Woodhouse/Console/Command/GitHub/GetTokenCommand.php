<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetTokenCommand extends Command
{
    public function __construct()
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        parent::__construct();
    }

    protected function configure()
    {
        $this->typeCheck->configure(func_get_args());

        $this->setName('github:get-token');
        $this->setDescription('Create (or get an existing) GitHub API token for Woodhouse.');

        $this->addArgument(
            'username',
            InputArgument::REQUIRED,
            'GitHub username.'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

    }

    private $typeCheck;
}
