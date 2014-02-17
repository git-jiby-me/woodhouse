<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\GitHub\GitHubClientFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAuthorizationCommand extends AbstractGitHubCommand
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

        $this->setName('github:create-auth');
        $this->setDescription('Create a new GitHub authorization.');

        $this->addOption(
            'scope',
            's',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'One or more access scopes.',
            array('repo')
        );
        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'The name to use when creating the authorization.'
        );
        $this->addOption(
            'url',
            null,
            InputOption::VALUE_REQUIRED,
            'The URL to use when creating the authorization.'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->createClient($input, $output);
        list($name, $url) = $this->authorizationDetails($input);

        $this->outputAuthorization(
            $output,
            $client->createAuthorization($input->getOption('scope'), $name, $url)
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return tuple<string|null,string|null>
     */
    protected function authorizationDetails(InputInterface $input)
    {
        $name = $input->getOption('name');
        $url = $input->getOption('url');
        if (null === $name && null === $url) {
            $name = static::DEFAULT_AUTHORIZATION_NAME;
            $url = static::DEFAULT_AUTHORIZATION_URL;
        }

        return array($name, $url);
    }

    }
