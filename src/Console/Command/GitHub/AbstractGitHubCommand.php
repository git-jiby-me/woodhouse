<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\GitHub\GitHubClientFactory;
use stdClass;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractGitHubCommand extends Command
{
    const DEFAULT_AUTHORIZATION_NAME = 'Woodhouse';
    const DEFAULT_AUTHORIZATION_URL = 'https://github.com/IcecaveStudios/woodhouse';

    /**
     * @param GitHubClientFactory|null $clientFactory
     */
    public function __construct(GitHubClientFactory $clientFactory = null)
    {
        if (null === $clientFactory) {
            $clientFactory = new GitHubClientFactory();
        }
        $this->clientFactory = $clientFactory;

        parent::__construct();
    }

    /**
     * @return GitHubClientFactory
     */
    public function clientFactory()
    {
        return $this->clientFactory;
    }

    /**
     * @param Application|null $application
     */
    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);

        if ($application) {
            $this->clientFactory->setUserAgent(
                $application->getName() . '/' . $application->getVersion()
            );
        }
    }

    protected function configure()
    {
        $this->addOption(
            'username',
            'u',
            InputOption::VALUE_REQUIRED,
            'A GitHub username to use for API authentication.'
        );
        $this->addOption(
            'password',
            'p',
            InputOption::VALUE_REQUIRED,
            'A GitHub password to use for API authentication.'
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return Icecave\Woodhouse\GitHub\GitHubClient;
     */
    protected function createClient(InputInterface $input, OutputInterface $output)
    {
        list($username, $password) = $this->credentials($input, $output);
        $client = $this->clientFactory()->create($username, $password);

        return $client;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return tuple<string,string>
     */
    protected function credentials(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        if ($input->isInteractive()) {
            if (null === $username) {
                $username = $this->getHelperSet()->get('dialog')->ask($output, 'Username: ');
            }
            if (null === $password) {
                $password = $this->getHelperSet()->get('hidden-input')->askHiddenResponse($output, 'Password: ');
            }
        }

        return array($username, $password);
    }

    /**
     * @param OutputInterface $output
     * @param array<stdClass> $authorizations
     */
    protected function outputAuthorizations(OutputInterface $output, array $authorizations)
    {
        foreach ($authorizations as $authorization) {
            $this->outputAuthorization($output, $authorization);
        }
    }

    /**
     * @param OutputInterface $output
     * @param stdClass        $authorization
     */
    protected function outputAuthorization(OutputInterface $output, stdClass $authorization)
    {
        $message = sprintf(
            '%d: %s <info>%s [%s]</info>',
            $authorization->id,
            $authorization->token,
            $authorization->app->name,
            implode(', ', $authorization->scopes)
        );
        if (null !== $authorization->note_url) {
            $message .= sprintf(
                ' <comment>%s</comment>',
                $authorization->note_url
            );
        } elseif (null !== $authorization->app->url) {
            $message .= sprintf(
                ' <comment>%s</comment>',
                $authorization->app->url
            );
        }

        $output->writeln($message);
    }

    private $clientFactory;
}
