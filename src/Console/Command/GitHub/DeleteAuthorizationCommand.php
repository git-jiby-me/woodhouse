<?php

namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\GitHub\GitHubClientFactory;
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
        parent::__construct($clientFactory);
    }

    protected function configure()
    {
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
        $client = $this->createClient($input, $output);
        $client->deleteAuthorization(
            intval($input->getArgument('id'))
        );

        $output->writeln('Authorization deleted.');
    }
}
