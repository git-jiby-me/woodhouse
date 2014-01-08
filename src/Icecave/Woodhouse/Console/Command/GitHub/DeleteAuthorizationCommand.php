<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\GitHub\GitHubClientFactory;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteAuthorizationCommand extends AbstractGitHubCommand
{
    /**
     * @param GitHubClientFactory|null $clientFactory
     */
    public function __construct(GitHubClientFactory $clientFactory = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        parent::__construct($clientFactory);
    }

    protected function configure()
    {
        $this->typeCheck->configure(func_get_args());

        parent::configure();

        $this->setName('github:delete-auth');
        $this->setDescription('Delete a GitHub authorization.');

        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'The ID of the authorization to delete.'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->typeCheck->execute(func_get_args());

        $client = $this->createClient($input, $output);
        $client->deleteAuthorization(
            intval($input->getArgument('id'))
        );

        $output->writeln('Authorization deleted.');
    }

    private $typeCheck;
}
