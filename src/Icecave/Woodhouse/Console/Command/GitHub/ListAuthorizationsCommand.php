<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\GitHub\GitHubClientFactory;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListAuthorizationsCommand extends AbstractGitHubCommand
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

        $this->setName('github:list-auth');
        $this->setDescription('List current GitHub authorizations.');

        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'Restrict to authorizations with a specific name (PCRE pattern).'
        );
        $this->addOption(
            'url',
            null,
            InputOption::VALUE_REQUIRED,
            'Restrict to authorizations with a specific URL (PCRE pattern).'
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
        list($namePattern, $urlPattern) = $this->patterns($input);

        $this->outputAuthorizations(
            $output,
            $client->authorizationsMatching(
                $namePattern,
                $urlPattern
            )
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return tuple<string|null,string|null>
     */
    protected function patterns(InputInterface $input)
    {
        $this->typeCheck->patterns(func_get_args());

        $namePattern = $input->getOption('name');
        $urlPattern = $input->getOption('url');

        if (null === $namePattern && null === $urlPattern) {
            $urlPattern = sprintf(
                '/^%s$/',
                preg_quote(static::DEFAULT_AUTHORIZATION_URL, '/')
            );
        }

        return array($namePattern, $urlPattern);
    }

    private $typeCheck;
}
