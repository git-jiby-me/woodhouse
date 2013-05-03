<?php
namespace Icecave\Woodhouse\Publisher;

use PHPUnit_Framework_TestCase;
use Phake;
use RuntimeException;
use Icecave\Isolator\Isolator;

class GitHubPublisherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_git = Phake::mock('Icecave\Woodhouse\Git\Git');
        $this->_fileSystem = Phake::mock('Symfony\Component\Filesystem\Filesystem');
        $this->_isolator = Phake::mock(get_class(Isolator::get()));

        $this->_diffProcess = Phake::mock('Symfony\Component\Process\Process');
        $this->_cloneProcess = Phake::mock('Symfony\Component\Process\Process');

        Phake::when($this->_isolator)
            ->sys_get_temp_dir()
            ->thenReturn('/tmp');

        Phake::when($this->_isolator)
            ->getmypid()
            ->thenReturn('10101');

        Phake::when($this->_isolator)
            ->is_dir('/source/bar')
            ->thenReturn(true);

        Phake::when($this->_git)
            ->cloneRepo(Phake::anyParameters())
            ->thenReturn($this->_cloneProcess);

        Phake::when($this->_diffProcess)
            ->getOutput()
            ->thenReturn('<diff output>');

        Phake::when($this->_git)
            ->diff(Phake::anyParameters())
            ->thenReturn($this->_diffProcess);

        Phake::when($this->_git)
            ->push(Phake::anyParameters())
            ->thenThrow(new RuntimeException)
            ->thenReturn(Phake::mock('Symfony\Component\Process\Process'));

        $this->_publisher = Phake::partialMock(
            __NAMESPACE__ . '\GitHubPublisher',
            $this->_git,
            $this->_fileSystem,
            $this->_isolator
        );
    }

    public function testConstructorDefaults()
    {
        $publisher = new GitHubPublisher;

        $this->assertInstanceOf('Icecave\Woodhouse\Git\Git', $publisher->git());
        $this->assertInstanceOf('Symfony\Component\Filesystem\Filesystem', $publisher->fileSystem());
    }

    public function testOutputFilter()
    {
        $filter = null;

        Phake::verify($this->_git)->setOutputFilter(Phake::capture($filter));

        $this->assertInstanceOf('Closure', $filter);

        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $output = $filter('The auth token 0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33 is in use.');

        $this->assertSame('The auth token 0bee********************************8a33 is in use.', $output);
    }

    public function testRepository()
    {
        $this->assertNull($this->_publisher->repository());
    }

    public function testSetRepository()
    {
        $this->_publisher->setRepository('foo/bar');
        $this->assertSame('foo/bar', $this->_publisher->repository());
    }

    public function testSetRepositoryFailure()
    {
        $this->assertNull($this->_publisher->repository());

        $this->setExpectedException('InvalidArgumentException', 'Invalid repository name: "foo:bar".');
        $this->_publisher->setRepository('foo:bar');
    }

    public function testRepositoryUrl()
    {
        $this->assertNull($this->_publisher->repositoryUrl());

        $this->_publisher->setRepository('foo/bar');

        $this->assertSame(
            'https://github.com/foo/bar.git',
            $this->_publisher->repositoryUrl()
        );

        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->assertSame(
            'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
            $this->_publisher->repositoryUrl()
        );
    }

    public function testBranch()
    {
        $this->assertSame('gh-pages', $this->_publisher->branch());
    }

    public function testSetBranch()
    {
        $this->_publisher->setBranch('some-branch');
        $this->assertSame('some-branch', $this->_publisher->branch());
    }

    public function testCommitMessage()
    {
        $this->assertSame('Content published by Woodhouse.', $this->_publisher->commitMessage());
    }

    public function testSetCommitMessage()
    {
        $this->_publisher->setCommitMessage('The message.');
        $this->assertSame('The message.', $this->_publisher->commitMessage());
    }

    public function testAuthToken()
    {
        $this->assertNull($this->_publisher->authToken());
    }

    public function testSetAuthToken()
    {
        $token = '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33';
        $this->_publisher->setAuthToken($token);
        $this->assertSame($token, $this->_publisher->authToken());
    }

    public function testSetAuthTokenFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid authentication token.');
        $this->_publisher->setAuthToken('foo');
    }

    public function testPublish()
    {
        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $result = $this->_publisher->publish();

        $pushVerifier = Phake::verify($this->_git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_git)->remove('bar-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_git)->add('bar-dest'),
            Phake::verify($this->_git)->diff(true),
            Phake::verify($this->_git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->_git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->_git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->_git)->pull(),
            $pushVerifier,
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishNoChanges()
    {
        Phake::when($this->_diffProcess)
            ->getOutput()
            ->thenReturn('    '); // whitespace only

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');

        $result = $this->_publisher->publish();

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_git)->diff(true)
        );

        $this->assertFalse($result);
    }

    public function testPublishMakeContentDirectories()
    {
        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/parent/foo-dest');

        $result = $this->_publisher->publish();

        $pushVerifier = Phake::verify($this->_git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('parent/foo-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_isolator)->is_dir('/tmp/woodhouse-10101/parent'),
            Phake::verify($this->_isolator)->mkdir('/tmp/woodhouse-10101/parent', 0777, true),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/parent/foo-dest'),
            Phake::verify($this->_git)->add('parent/foo-dest'),
            Phake::verify($this->_git)->diff(true),
            Phake::verify($this->_git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->_git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->_git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->_git)->pull(),
            $pushVerifier,
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishToNewBranch()
    {
        Phake::when($this->_git)
            ->cloneRepo(Phake::anyParameters())
            ->thenThrow(new RuntimeException('... test-branch not found in upstream origin ...'))
            ->thenReturn($this->_cloneProcess);

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $result = $this->_publisher->publish();

        $pushVerifier = Phake::verify($this->_git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                null,
                0
            ),
            Phake::verify($this->_git)->checkout('test-branch', true),
            Phake::verify($this->_git)->remove('.'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_git)->add('bar-dest'),
            Phake::verify($this->_git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->_git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->_git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->_git)->pull(),
            $pushVerifier,
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishSuccessPushAttemptsExhausted()
    {
        Phake::when($this->_git)
            ->push(Phake::anyParameters())
            ->thenThrow(new RuntimeException)
            ->thenThrow(new RuntimeException)
            ->thenReturn(Phake::mock('Symfony\Component\Process\Process'));

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $this->assertTrue($this->_publisher->publish());

        $pushVerifier = Phake::verify($this->_git, Phake::times(3))->push('origin', 'test-branch');
        $pullVerifier = Phake::verify($this->_git, Phake::times(2))->pull();

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_git)->remove('bar-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_git)->add('bar-dest'),
            Phake::verify($this->_git)->diff(true),
            Phake::verify($this->_git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->_git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->_git)->commit('Test commit message.'),
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailurePushAttemptsExhausted()
    {
        $exception = new RuntimeException;

        Phake::when($this->_git)
            ->push(Phake::anyParameters())
            ->thenThrow($exception);

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        try {
            $this->_publisher->publish();
        } catch (RuntimeException $e) {
            $this->assertSame($exception, $e);
        }

        $pushVerifier = Phake::verify($this->_git, Phake::times(3))->push('origin', 'test-branch');
        $pullVerifier = Phake::verify($this->_git, Phake::times(2))->pull();

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_git)->remove('bar-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_git)->add('bar-dest'),
            Phake::verify($this->_git)->diff(true),
            Phake::verify($this->_git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->_git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->_git)->commit('Test commit message.'),
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailureNoRepository()
    {
        $this->setExpectedException('RuntimeException', 'No repository set.');
        $this->_publisher->publish();
    }

    public function testPublishCloneFailure()
    {
        $exception = new RuntimeException;

        Phake::when($this->_git)
            ->cloneRepo(Phake::anyParameters())
            ->thenThrow($exception);

        $this->_publisher->setRepository('foo/bar');

        try {
            $this->_publisher->publish();
            $this->fail('Expected exception did not propagate.');
        } catch (RuntimeException $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function testDryRun()
    {
        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $result = $this->_publisher->dryRun();

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_git)->remove('bar-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_git)->add('bar-dest'),
            Phake::verify($this->_git)->diff(true),
            Phake::verify($this->_fileSystem)->remove('/tmp/woodhouse-10101')
        );

        Phake::verify($this->_git, Phake::never())->commit(Phake::anyParameters());
        Phake::verify($this->_git, Phake::never())->push(Phake::anyParameters());

        $this->assertTrue($result);
    }

    public function testDryRunNoChanges()
    {
        Phake::when($this->_diffProcess)
            ->getOutput()
            ->thenReturn('    '); // whitespace only

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');

        $result = $this->_publisher->dryRun();

        Phake::inOrder(
            Phake::verify($this->_git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                0
            ),
            Phake::verify($this->_git)->remove('foo-dest'),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_git)->add('foo-dest'),
            Phake::verify($this->_git)->diff(true)
        );

        Phake::verify($this->_git, Phake::never())->commit(Phake::anyParameters());
        Phake::verify($this->_git, Phake::never())->push(Phake::anyParameters());

        $this->assertFalse($result);
    }
}
