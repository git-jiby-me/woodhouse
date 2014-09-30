<?php
namespace Icecave\Woodhouse\Console\Command;

use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Icecave\Woodhouse\BuildStatus\StatusImageSelector;
use Icecave\Woodhouse\BuildStatus\StatusReaderFactory;
use Icecave\Woodhouse\Coverage\CoverageImageSelector;
use Icecave\Woodhouse\Coverage\CoverageReaderFactory;
use Icecave\Woodhouse\Publisher\GitHubPublisher;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommand extends Command
{
    /**
     * @param GitHubPublisher|null       $publisher
     * @param StatusReaderFactory|null   $statusReaderFactory
     * @param StatusImageSelector|null   $statusImageSelector
     * @param CoverageReaderFactory|null $coverageReaderFactory
     * @param CoverageImageSelector|null $coverageImageSelector
     * @param Isolator|null              $isolator
     */
    public function __construct(
        GitHubPublisher $publisher = null,
        StatusReaderFactory $statusReaderFactory = null,
        StatusImageSelector $statusImageSelector = null,
        CoverageReaderFactory $coverageReaderFactory = null,
        CoverageImageSelector $coverageImageSelector = null,
        Isolator $isolator = null
    ) {
        $this->isolator = Isolator::get($isolator);

        if (null === $publisher) {
            $publisher = new GitHubPublisher(null, null, $this->isolator);
        }

        if (null === $statusReaderFactory) {
            $statusReaderFactory = new StatusReaderFactory($this->isolator);
        }

        if (null === $statusImageSelector) {
            $statusImageSelector = new StatusImageSelector();
        }

        if (null === $coverageReaderFactory) {
            $coverageReaderFactory = new CoverageReaderFactory($this->isolator);
        }

        if (null === $coverageImageSelector) {
            $coverageImageSelector = new CoverageImageSelector();
        }

        $this->publisher = $publisher;
        $this->statusReaderFactory = $statusReaderFactory;
        $this->statusImageSelector = $statusImageSelector;
        $this->coverageReaderFactory = $coverageReaderFactory;
        $this->coverageImageSelector = $coverageImageSelector;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('publish');
        $this->setDescription('Publish content to a GitHub repository.');

        // Arguments ...

        $this->addArgument(
            'repository',
            InputArgument::REQUIRED,
            'The name of the GitHub repository to which content is published (eg: IcecaveStudios/woodhouse).'
        );

        $this->addArgument(
            'content',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Pairs of source/destination folders separated by colons (eg: /path/to/source-folder:/path/to/destination-folder).'
        );

        // Git options ...

        $this->addOption(
            'message',
            'm',
            InputOption::VALUE_REQUIRED,
            'Use the given message as the Git commit message.'
        );

        $this->addOption(
            'branch',
            'b',
            InputOption::VALUE_REQUIRED,
            'The target branch of the repository.',
            'gh-pages'
        );

        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Prepare for publication but do not make any changes.'
        );

        // GitHub options ...

        $this->addOption(
            'auth-token',
            't',
            InputOption::VALUE_REQUIRED,
            'Use the given GitHub OAuth API token for authentication.'
        );

        $this->addOption(
            'auth-token-env',
            'e',
            InputOption::VALUE_REQUIRED,
            'Use the given GitHub OAuth API token for authentication, the value should be the name of an environment variable containing the token.'
        );

        // Build status options ...

        $this->addOption(
            'build-status-image',
            's',
            InputOption::VALUE_REQUIRED,
            'Publish a coverage badge to the given location (requires one of the other --build-status-* options).'
        );

        $this->addOption(
            'build-status-phpunit',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given PHPUnit JSON test report to determine the build status.'
        );

        $this->addOption(
            'build-status-tap',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given TAP test report to determine the build status.'
        );

        $this->addOption(
            'build-status-junit',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given JUnit XML test report to determine the build status.'
        );

        $this->addOption(
            'build-status-result',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given result as the build status (options: passing, failing, pending, unknown, error).'
        );

        // Coverage image options ...

        $this->addOption(
            'coverage-image',
            'c',
            InputOption::VALUE_REQUIRED,
            'Publish a coverage badge to the given location (requires one of the other --coverage-* options).'
        );

        $this->addOption(
            'coverage-phpunit',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given PHPUnit Text code coverage report to determine the coverage percentage.'
        );

        $this->addOption(
            'coverage-percentage',
            null,
            InputOption::VALUE_REQUIRED,
            'Use the given value as the coverage percentage.'
        );

        // Other ...

        $this->addOption(
            'image-theme',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Use an alternative theme for the build status and coverage images.',
            array('travis/variable-width')
        );
    }

    /**
     * @param mixed          $factory
     * @param string         $optionPrefix
     * @param InputInterface $input
     *
     * @return tuple<string|null, string|null>
     */
    protected function resolveReader($factory, $optionPrefix, InputInterface $input)
    {
        $reader = null;
        $option = null;

        // Select the appropriate status reader ...
        foreach ($factory->supportedTypes() as $type) {
            $optionName = $optionPrefix . '-' . $type;
            if ($value = $input->getOption($optionName)) {
                if (null === $option) {
                    $reader = $factory->create($type, $value);
                    $option = $optionName;
                } else {
                    throw new RuntimeException('--' . $optionName . ' is incompatible with --' . $option . '.');
                }
            }
        }

        return array($option, $reader);
    }

    /**
     * @param InputInterface $input
     *
     * @return array<string, string>
     */
    public function resolveThemes(InputInterface $input)
    {
        $themesRoot = $this->getApplication()->vendorPath() . '/ezzatron/ci-status-images/img';
        $themes = array();

        foreach ($input->getOption('image-theme') as $theme) {
            $themePath = $themesRoot . '/' . $theme;
            if (!$this->isolator->is_dir($themePath)) {
                throw new RuntimeException('Unknown image theme "' . $theme . '".');
            }
            $themes[$theme] = $themePath;
        }

        return $themes;
    }

    /**
     * @param array<string, string> $themes
     * @param string                $targetPath
     * @param string                $category
     * @param string                $filename
     */
    public function enqueueImages(array $themes, $targetPath, $category, $filename)
    {
        // If there is only a single theme, publish the image directly to the target path.
        if (count($themes) === 1) {
            $source = current($themes) . '/' . $category . '/' . $filename;
            $this->publisher->add($source, $targetPath);

            return;
        }

        // If there are multiple themes publish the image once for each theme.
        // For example, "/artifacts/coverage.png" becomes "/artifacts/<theme>/<variant>/coverage.png"
        $dirname  = dirname($targetPath);
        $basename = basename($targetPath);

        foreach ($themes as $theme => $themePath) {
            $source = $themePath . '/' . $category . '/' . $filename;
            $target = $dirname . '/' . $theme . '/' . $basename;
            $this->publisher->add($source, $target);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($statusType, $statusReader)     = $this->resolveReader($this->statusReaderFactory, 'build-status', $input);
        list($coverageType, $coverageReader) = $this->resolveReader($this->coverageReaderFactory, 'coverage', $input);
        $imageThemes                         = $this->resolveThemes($input);

        // Enqueue build status images for publication ...
        if ($statusImageTarget = $input->getOption('build-status-image')) {
            if (null !== $statusType) {
                $status = $statusReader->readStatus();
            } elseif ($input->getOption('no-interaction')) {
                $status = BuildStatus::ERROR();
            } else {
                throw new RuntimeException('--build-status-image requires one of the other --build-status-* options.');
            }
            $filename = $this->statusImageSelector->imageFilename($status);
            $this->enqueueImages($imageThemes, $statusImageTarget, 'build-status', $filename);
        } elseif ($statusType) {
            throw new RuntimeException('--' . $statusType . ' requires --build-status-image.');
        }

        // Enqueue test coverage images for publication ...
        if ($coverageImageTarget = $input->getOption('coverage-image')) {
            if (null !== $coverageType) {
                $percentage = $coverageReader->readPercentage();
                $filename = $this->coverageImageSelector->imageFilename($percentage);
            } elseif ($input->getOption('no-interaction')) {
                $filename = $this->coverageImageSelector->errorImageFilename();
            } else {
                throw new RuntimeException('--coverage-image requires one of the other --coverage-* options.');
            }

            $this->enqueueImages($imageThemes, $coverageImageTarget, 'test-coverage', $filename);
        } elseif ($coverageType) {
            throw new RuntimeException('--' . $coverageType . ' requires --coverage-image.');
        }

        // Enqueue content ...
        foreach ($input->getArgument('content') as $content) {
            $index = strrpos($content, ':');
            if (false === $index) {
                throw new RuntimeException('Invalid content specifier: "' . $content . '", content must be specified as colon separated pairs of source and destination path.');
            }

            $sourcePath = substr($content, 0, $index);
            $targetPath = substr($content, $index + 1);

            if (!$this->isolator->file_exists($sourcePath)) {
                throw new RuntimeException('Content does not exist: "' . $sourcePath . '".');
            }

            if (!preg_match('{^([a-z]:[\\\\/]|/)}i', $sourcePath)) {
                $sourcePath = $this->isolator->getcwd() . '/' . $sourcePath;
            }

            $this->publisher->add(
                $sourcePath,
                $targetPath
            );
        }

        // Set the authentication token ...
        $authToken = $input->getOption('auth-token');
        if ($authTokenEnv = $input->getOption('auth-token-env')) {
            if (null === $authToken) {
                $authToken = $this->isolator->getenv($authTokenEnv);
            } else {
                throw new RuntimeException('--auth-token-env is incompatible with --auth-token.');
            }
        }

        if ($message = $input->getOption('message')) {
            $this->publisher->setCommitMessage($message);
        }

        $this->publisher->setAuthToken($authToken);
        $this->publisher->setRepository($input->getArgument('repository'));
        $this->publisher->setBranch($input->getOption('branch'));

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(
                sprintf(
                    'Publishing to <info>%s</info> at <info>%s</info>:',
                    $input->getOption('branch'),
                    $input->getArgument('repository')
                )
            );

            foreach ($this->publisher->contentPaths() as $sourcePath => $targetPath) {
                $output->writeln(
                    sprintf(
                        ' * <info>%s</info> -> <info>%s</info>',
                        $sourcePath,
                        $targetPath
                    )
                );
            }
        }

        if ($input->getOption('dry-run')) {
            if ($this->publisher->dryRun()) {
                $output->writeln('Content prepared successfully (dry run).');
            } else {
                $output->writeln('No changes to publish (dry run).');
            }
        } else {
            if ($this->publisher->publish()) {
                $output->writeln('Content published successfully.');
            } else {
                $output->writeln('No changes to publish.');
            }
        }
    }

        private $publisher;
    private $statusReaderFactory;
    private $statusImageSelector;
    private $coverageReaderFactory;
    private $coverageImageSelector;
    private $isolator;
}
