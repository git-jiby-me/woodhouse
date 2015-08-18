<?php

namespace Icecave\Woodhouse\Console\Command;

use Eloquent\Liberator\Liberator;
use Icecave\Woodhouse\Console\Application;
use Phake;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class PublishCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->publisher             = Phake::mock('Icecave\Woodhouse\Publisher\GitHubPublisher');
        $this->statusReaderFactory   = Phake::partialMock('Icecave\Woodhouse\BuildStatus\StatusReaderFactory');
        $this->statusImageSelector   = Phake::partialMock('Icecave\Woodhouse\BuildStatus\StatusImageSelector');
        $this->coverageReaderFactory = Phake::partialMock('Icecave\Woodhouse\Coverage\CoverageReaderFactory');
        $this->coverageImageSelector = Phake::partialMock('Icecave\Woodhouse\Coverage\CoverageImageSelector');
        $this->isolator              = Phake::mock('Icecave\Isolator\Isolator');

        Phake::when($this->isolator)
            ->realpath(Phake::anyParameters())
            ->thenCallParent();

        Phake::when($this->isolator)
            ->getcwd()
            ->thenReturn('/current/dir');

        Phake::when($this->isolator)
            ->is_dir(Phake::anyParameters())
            ->thenReturn(true);

        Phake::when($this->isolator)
            ->file_exists(Phake::anyParameters())
            ->thenReturn(true);

        Phake::when($this->publisher)
            ->publish()
            ->thenReturn(true);

        Phake::when($this->publisher)
            ->dryRun()
            ->thenReturn(true);

        $this->application = new Application('/path/to/vendors');

        $this->command = new PublishCommand(
            $this->publisher,
            $this->statusReaderFactory,
            $this->statusImageSelector,
            $this->coverageReaderFactory,
            $this->coverageImageSelector,
            $this->isolator
        );

        $this->command->setApplication($this->application);

        $this->output = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
    }

    public function testConstructorDefaults()
    {
        $command = new PublishCommand();
        $command = Liberator::liberate($command);

        $this->assertInstanceOf('Icecave\Woodhouse\Publisher\GitHubPublisher', $command->publisher);
        $this->assertInstanceOf('Icecave\Woodhouse\BuildStatus\StatusReaderFactory', $command->statusReaderFactory);
        $this->assertInstanceOf('Icecave\Woodhouse\BuildStatus\StatusImageSelector', $command->statusImageSelector);
        $this->assertInstanceOf('Icecave\Woodhouse\Coverage\CoverageReaderFactory', $command->coverageReaderFactory);
        $this->assertInstanceOf('Icecave\Woodhouse\Coverage\CoverageImageSelector', $command->coverageImageSelector);
    }

    public function testConfigure()
    {
        $this->assertSame('publish', $this->command->getName());
        $this->assertSame('Publish content to a GitHub repository.', $this->command->getDescription());
    }

    public function testExecute()
    {
        // Double escape backslashes, once for PHP and once for command line parser ...
        $input = new StringInput('publish foo/bar c:\\\\foo\\\\bar:dest-a /foo/bar:dest-b');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('c:\foo\bar', 'dest-a'),
            Phake::verify($this->publisher)->add('/foo/bar', 'dest-b'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteNoChanges()
    {
        Phake::when($this->publisher)
            ->publish()
            ->thenReturn(false);

        // Double escape backslashes, once for PHP and once for command line parser ...
        $input = new StringInput('publish foo/bar c:\\\\foo\\\\bar:dest-a /foo/bar:dest-b');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('c:\foo\bar', 'dest-a'),
            Phake::verify($this->publisher)->add('/foo/bar', 'dest-b'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('No changes to publish.')
        );
    }

    public function testExecuteWithAuthToken()
    {
        $input = new StringInput('publish foo/bar a:b c:d --auth-token 0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithAuthTokenEnv()
    {
        Phake::when($this->isolator)
            ->getenv('TOKEN_VAR')
            ->thenReturn('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $input = new StringInput('publish foo/bar a:b c:d --auth-token-env TOKEN_VAR');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureWithAuthTokenAndAuthTokenEnv()
    {
        $input = new StringInput('publish foo/bar a:b c:d --auth-token xxx --auth-token-env TOKEN_VAR');

        $this->setExpectedException('RuntimeException', '--auth-token-env is incompatible with --auth-token.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteWithCommitMessage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --message "This is the message!"');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setCommitMessage('This is the message!'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureNoContent()
    {
        Phake::when($this->isolator)
            ->file_exists('a')
            ->thenReturn(false);

        $input = new StringInput('publish foo/bar a:b c:d');

        $this->setExpectedException('RuntimeException', 'Content does not exist: "a".');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureNoArguments()
    {
        $input = new StringInput('publish');

        $this->setExpectedException('RuntimeException', 'Not enough arguments.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureWithInvalidContentSpecifier()
    {
        $input = new StringInput('publish foo/bar a');

        $this->setExpectedException('RuntimeException', 'Invalid content specifier: "a", content must be specified as colon separated pairs of source and destination path.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteWithUnknownTheme()
    {
        Phake::when($this->isolator)
               ->is_dir('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width')
               ->thenReturn(false);

        $input = new StringInput('publish foo/bar a:b c:d --build-status-image status.png --build-status-result passing');

        $this->setExpectedException('RuntimeException', 'Unknown image theme "travis/variable-width".');
        $this->command->run($input, $this->output);
    }

    public function testExecuteWithBuildStatusImage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --build-status-image status.png --build-status-result passing');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/build-status/build-status-passing.png', 'status.png'),
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithBuildStatusImageWithMultipleThemes()
    {
        $input = new StringInput('publish foo/bar a:b c:d --build-status-image images/status.png --build-status-result passing --image-theme theme1/variant --image-theme theme2/variant');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme1/variant/build-status/build-status-passing.png', 'images/theme1/variant/status.png'),
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme2/variant/build-status/build-status-passing.png', 'images/theme2/variant/status.png'),
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureMultipleBuildStatuses()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-result passing --build-status-phpunit /foo/bar');
        $this->setExpectedException('RuntimeException', '--build-status-result is incompatible with --build-status-phpunit.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureBuildStatusImageWithNoStatus()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-image /foo/bar');

        $this->setExpectedException('RuntimeException', '--build-status-image requires one of the other --build-status-* options.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureBuildStatusWithNoImage()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-result passing');

        $this->setExpectedException('RuntimeException', '--build-status-result requires --build-status-image.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteWithCoverageImage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --coverage-image coverage.png --coverage-percentage 50');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/test-coverage/test-coverage-050.png', 'coverage.png'),
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithCoverageImageWithMultipleThemes()
    {
        $input = new StringInput('publish foo/bar a:b c:d --coverage-image images/coverage.png --coverage-percentage 50 --image-theme theme1/variant --image-theme theme2/variant');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme1/variant/test-coverage/test-coverage-050.png', 'images/theme1/variant/coverage.png'),
            Phake::verify($this->publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme2/variant/test-coverage/test-coverage-050.png', 'images/theme2/variant/coverage.png'),
            Phake::verify($this->publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->publisher)->setAuthToken(null),
            Phake::verify($this->publisher)->setRepository('foo/bar'),
            Phake::verify($this->publisher)->setBranch('gh-pages'),
            Phake::verify($this->publisher)->publish(),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureMultipleCoveragePercentages()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-percentage 50 --coverage-phpunit /foo/bar');
        $this->setExpectedException('RuntimeException', '--coverage-phpunit is incompatible with --coverage-percentage.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureCoverageImageWithNoPercentage()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-image /foo/bar');

        $this->setExpectedException('RuntimeException', '--coverage-image requires one of the other --coverage-* options.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteFailureCoverageWithNoImage()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-percentage 50');

        $this->setExpectedException('RuntimeException', '--coverage-percentage requires --coverage-image.');
        $this->command->run($input, $this->output);
    }

    public function testExecuteWithVerbose()
    {
        Phake::when($this->output)
            ->getVerbosity()
            ->thenReturn(OutputInterface::VERBOSITY_VERBOSE);

        Phake::when($this->publisher)
            ->contentPaths()
            ->thenReturn(
                array('/path/to/source' => '/path/to/target')
            );

        $input = new StringInput('publish foo/bar a:b --verbose');

        $this->command->run($input, $this->output);

        Phake::inOrder(
            Phake::verify($this->output)->writeln('Publishing to <info>gh-pages</info> at <info>foo/bar</info>:'),
            Phake::verify($this->output)->writeln(' * <info>/path/to/source</info> -> <info>/path/to/target</info>'),
            Phake::verify($this->output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithDryRun()
    {
        $input = new StringInput('publish foo/bar a:b --dry-run');

        $this->command->run($input, $this->output);

        Phake::verify($this->publisher)->dryRun();
        Phake::verify($this->publisher, Phake::never())->publish();
        Phake::verify($this->output)->writeln('Content prepared successfully (dry run).');
    }

    public function testExecuteWithDryRunNoChanges()
    {
        Phake::when($this->publisher)
            ->dryRun()
            ->thenReturn(false);

        $input = new StringInput('publish foo/bar a:b --dry-run');

        $this->command->run($input, $this->output);

        Phake::verify($this->publisher)->dryRun();
        Phake::verify($this->publisher, Phake::never())->publish();
        Phake::verify($this->output)->writeln('No changes to publish (dry run).');
    }

    public function testExecuteWithErrorBuildStatusWhenNonInteractive()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-image status.png --no-interaction');

        $this->command->run($input, $this->output);

        Phake::verify($this->publisher)->add(
            '/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/build-status/build-status-error.png',
            'status.png'
        );
    }

    public function testExecuteWithErrorCoverageWhenNonInteractive()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-image coverage.png --no-interaction');

        $this->command->run($input, $this->output);

        Phake::verify($this->publisher)->add(
            '/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/test-coverage/test-coverage-error.png',
            'coverage.png'
        );
    }
}
