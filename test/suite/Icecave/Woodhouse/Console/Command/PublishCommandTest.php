<?php
namespace Icecave\Woodhouse\Console\Command;

use Icecave\Woodhouse\Console\Application;
use PHPUnit_Framework_TestCase;
use Phake;
use Symfony\Component\Console\Input\StringInput;
use Eloquent\Liberator\Liberator;

class PublishCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_publisher             = Phake::mock('Icecave\Woodhouse\Publisher\GitHubPublisher');
        $this->_statusReaderFactory   = Phake::partialMock('Icecave\Woodhouse\BuildStatus\StatusReaderFactory');
        $this->_statusImageSelector   = Phake::partialMock('Icecave\Woodhouse\BuildStatus\StatusImageSelector');
        $this->_coverageReaderFactory = Phake::partialMock('Icecave\Woodhouse\Coverage\CoverageReaderFactory');
        $this->_coverageImageSelector = Phake::partialMock('Icecave\Woodhouse\Coverage\CoverageImageSelector');
        $this->_isolator              = Phake::mock('Icecave\Isolator\Isolator');

        Phake::when($this->_isolator)
            ->realpath(Phake::anyParameters())
            ->thenCallParent();

        Phake::when($this->_isolator)
            ->getcwd()
            ->thenReturn('/current/dir');

        Phake::when($this->_isolator)
            ->is_dir(Phake::anyParameters())
            ->thenReturn(true);

        Phake::when($this->_isolator)
            ->file_exists(Phake::anyParameters())
            ->thenReturn(true);

        Phake::when($this->_publisher)
            ->publish()
            ->thenReturn(true);

        $this->_application = new Application('/path/to/vendors');

        $this->_command = new PublishCommand(
            $this->_publisher,
            $this->_statusReaderFactory,
            $this->_statusImageSelector,
            $this->_coverageReaderFactory,
            $this->_coverageImageSelector,
            $this->_isolator
        );

        $this->_command->setApplication($this->_application);

        $this->_output = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
    }

    public function testConstructorDefaults()
    {
        $command = new PublishCommand;
        $command = Liberator::liberate($command);

        $this->assertInstanceOf('Icecave\Woodhouse\Publisher\GitHubPublisher', $command->publisher);
        $this->assertInstanceOf('Icecave\Woodhouse\BuildStatus\StatusReaderFactory', $command->statusReaderFactory);
        $this->assertInstanceOf('Icecave\Woodhouse\BuildStatus\StatusImageSelector', $command->statusImageSelector);
        $this->assertInstanceOf('Icecave\Woodhouse\Coverage\CoverageReaderFactory', $command->coverageReaderFactory);
        $this->assertInstanceOf('Icecave\Woodhouse\Coverage\CoverageImageSelector', $command->coverageImageSelector);
    }

    public function testConfigure()
    {
        $this->assertSame('publish', $this->_command->getName());
        $this->assertSame('Publish content to a GitHub repository.', $this->_command->getDescription());
    }

    public function testExecute()
    {
        // Double escape backslashes, once for PHP and once for command line parser ...
        $input = new StringInput('publish foo/bar c:\\\\foo\\\\bar:dest-a /foo/bar:dest-b');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('c:\foo\bar', 'dest-a'),
            Phake::verify($this->_publisher)->add('/foo/bar', 'dest-b'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteNoChanges()
    {
        Phake::when($this->_publisher)
            ->publish()
            ->thenReturn(false);

        // Double escape backslashes, once for PHP and once for command line parser ...
        $input = new StringInput('publish foo/bar c:\\\\foo\\\\bar:dest-a /foo/bar:dest-b');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('c:\foo\bar', 'dest-a'),
            Phake::verify($this->_publisher)->add('/foo/bar', 'dest-b'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('No changes to publish.')
        );
    }

    public function testExecuteWithAuthToken()
    {
        $input = new StringInput('publish foo/bar a:b c:d --auth-token 0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithAuthTokenEnv()
    {
        Phake::when($this->_isolator)
            ->getenv('TOKEN_VAR')
            ->thenReturn('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $input = new StringInput('publish foo/bar a:b c:d --auth-token-env TOKEN_VAR');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33'),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureWithAuthTokenAndAuthTokenEnv()
    {
        $input = new StringInput('publish foo/bar a:b c:d --auth-token xxx --auth-token-env TOKEN_VAR');

        $this->setExpectedException('RuntimeException', '--auth-token-env is incompatible with --auth-token.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteWithCommitMessage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --message "This is the message!"');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setCommitMessage('This is the message!'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureNoContent()
    {
        Phake::when($this->_isolator)
            ->file_exists('a')
            ->thenReturn(false);

        $input = new StringInput('publish foo/bar a:b c:d');

        $this->setExpectedException('RuntimeException', 'Content does not exist: "a".');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureNoArguments()
    {
        $input = new StringInput('publish');

        $this->setExpectedException('RuntimeException', 'Not enough arguments.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureWithInvalidContentSpecifier()
    {
        $input = new StringInput('publish foo/bar a');

        $this->setExpectedException('RuntimeException', 'Invalid content specifier: "a", content must be specified as colon separated pairs of source and destination path.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteWithUnknownTHeme()
    {
        Phake::when($this->_isolator)
               ->is_dir('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width')
               ->thenReturn(false);

        $input = new StringInput('publish foo/bar a:b c:d --build-status-image status.png --build-status-result passing');

        $this->setExpectedException('RuntimeException', 'Unknown image theme "travis/variable-width".');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteWithBuildStatusImage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --build-status-image status.png --build-status-result passing');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/build-status/build-status-passing.png', 'status.png'),
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithBuildStatusImageWithMultipleThemes()
    {
        $input = new StringInput('publish foo/bar a:b c:d --build-status-image images/status.png --build-status-result passing --image-theme theme1/variant --image-theme theme2/variant');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme1/variant/build-status/build-status-passing.png', 'images/theme1/variant/status.png'),
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme2/variant/build-status/build-status-passing.png', 'images/theme2/variant/status.png'),
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureMultipleBuildStatuses()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-result passing --build-status-phpunit /foo/bar');
        $this->setExpectedException('RuntimeException', '--build-status-result is incompatible with --build-status-phpunit.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureBuildStatusImageWithNoStatus()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-image /foo/bar');

        $this->setExpectedException('RuntimeException', '--build-status-image requires one of the other --build-status-* options.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureBuildStatusWithNoImage()
    {
        $input = new StringInput('publish foo/bar a:b --build-status-result passing');

        $this->setExpectedException('RuntimeException', '--build-status-result requires --build-status-image.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteWithCoverageImage()
    {
        $input = new StringInput('publish foo/bar a:b c:d --coverage-image coverage.png --coverage-percentage 50');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/travis/variable-width/test-coverage/test-coverage-050.png', 'coverage.png'),
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteWithCoverageImageWithMultipleThemes()
    {
        $input = new StringInput('publish foo/bar a:b c:d --coverage-image images/coverage.png --coverage-percentage 50 --image-theme theme1/variant --image-theme theme2/variant');

        $this->_command->run($input, $this->_output);

        Phake::inOrder(
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme1/variant/test-coverage/test-coverage-050.png', 'images/theme1/variant/coverage.png'),
            Phake::verify($this->_publisher)->add('/path/to/vendors/ezzatron/ci-status-images/img/theme2/variant/test-coverage/test-coverage-050.png', 'images/theme2/variant/coverage.png'),
            Phake::verify($this->_publisher)->add('/current/dir/a', 'b'),
            Phake::verify($this->_publisher)->add('/current/dir/c', 'd'),
            Phake::verify($this->_publisher)->setAuthToken(null),
            Phake::verify($this->_publisher)->setRepository('foo/bar'),
            Phake::verify($this->_publisher)->setBranch('gh-pages'),
            Phake::verify($this->_publisher)->publish(),
            Phake::verify($this->_output)->writeln('Content published successfully.')
        );
    }

    public function testExecuteFailureMultipleCoveragePercentages()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-percentage 50 --coverage-phpunit /foo/bar');
        $this->setExpectedException('RuntimeException', '--coverage-phpunit is incompatible with --coverage-percentage.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureCoverageImageWithNoPercentage()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-image /foo/bar');

        $this->setExpectedException('RuntimeException', '--coverage-image requires one of the other --coverage-* options.');
        $this->_command->run($input, $this->_output);
    }

    public function testExecuteFailureCoverageWithNoImage()
    {
        $input = new StringInput('publish foo/bar a:b --coverage-percentage 50');

        $this->setExpectedException('RuntimeException', '--coverage-percentage requires --coverage-image.');
        $this->_command->run($input, $this->_output);
    }
}
