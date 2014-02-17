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
        $this->git = Phake::mock('Icecave\Woodhouse\Git\Git');
        $this->fileSystem = Phake::mock('Symfony\Component\Filesystem\Filesystem');
        $this->isolator = Phake::mock(get_class(Isolator::get()));

        $this->diffProcess = Phake::mock('Symfony\Component\Process\Process');
        $this->cloneProcess = Phake::mock('Symfony\Component\Process\Process');

        Phake::when($this->isolator)
            ->sys_get_temp_dir()
            ->thenReturn('/tmp');

        Phake::when($this->isolator)
            ->getmypid()
            ->thenReturn('10101');

        Phake::when($this->isolator)
            ->is_dir('/source/bar')
            ->thenReturn(true);

        Phake::when($this->git)
            ->cloneRepo(Phake::anyParameters())
            ->thenReturn($this->cloneProcess);

        Phake::when($this->diffProcess)
            ->getOutput()
            ->thenReturn('<diff output>');

        Phake::when($this->git)
            ->diff(Phake::anyParameters())
            ->thenReturn($this->diffProcess);

        Phake::when($this->git)
            ->push(Phake::anyParameters())
            ->thenThrow(new RuntimeException)
            ->thenReturn(Phake::mock('Symfony\Component\Process\Process'));

        $this->publisher = Phake::partialMock(
            __NAMESPACE__ . '\GitHubPublisher',
            $this->git,
            $this->fileSystem,
            $this->isolator
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

        Phake::verify($this->git)->setOutputFilter(Phake::capture($filter));

        $this->assertInstanceOf('Closure', $filter);

        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $output = $filter('The auth token 0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33 is in use.');

        $this->assertSame('The auth token 0bee********************************8a33 is in use.', $output);
    }

    public function testRepository()
    {
        $this->assertNull($this->publisher->repository());
    }

    public function testSetRepository()
    {
        $this->publisher->setRepository('foo/bar');
        $this->assertSame('foo/bar', $this->publisher->repository());
    }

    public function testSetRepositoryFailure()
    {
        $this->assertNull($this->publisher->repository());

        $this->setExpectedException('InvalidArgumentException', 'Invalid repository name: "foo:bar".');
        $this->publisher->setRepository('foo:bar');
    }

    public function testRepositoryUrl()
    {
        $this->assertNull($this->publisher->repositoryUrl());

        $this->publisher->setRepository('foo/bar');

        $this->assertSame(
            'https://github.com/foo/bar.git',
            $this->publisher->repositoryUrl()
        );

        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->assertSame(
            'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
            $this->publisher->repositoryUrl()
        );
    }

    public function testBranch()
    {
        $this->assertSame('gh-pages', $this->publisher->branch());
    }

    public function testSetBranch()
    {
        $this->publisher->setBranch('some-branch');
        $this->assertSame('some-branch', $this->publisher->branch());
    }

    public function testCommitMessage()
    {
        $this->assertSame('Content published by Woodhouse.', $this->publisher->commitMessage());
    }

    public function testSetCommitMessage()
    {
        $this->publisher->setCommitMessage('The message.');
        $this->assertSame('The message.', $this->publisher->commitMessage());
    }

    public function testAuthToken()
    {
        $this->assertNull($this->publisher->authToken());
    }

    public function testSetAuthToken()
    {
        $token = '0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33';
        $this->publisher->setAuthToken($token);
        $this->assertSame($token, $this->publisher->authToken());
    }

    public function testSetAuthTokenFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid authentication token.');
        $this->publisher->setAuthToken('foo');
    }

    public function testPublish()
    {
        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');
        $this->publisher->add('/source/bar', '/bar-dest');

        $result = $this->publisher->publish();

        $pushVerifier = Phake::verify($this->git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->git)->remove('bar-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->git)->add('bar-dest'),
            Phake::verify($this->git)->diff(true),
            Phake::verify($this->git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->git)->pull(),
            $pushVerifier,
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishNoChanges()
    {
        Phake::when($this->diffProcess)
            ->getOutput()
            ->thenReturn('    '); // whitespace only

        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');

        $result = $this->publisher->publish();

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->git)->diff(true)
        );

        $this->assertFalse($result);
    }

    public function testPublishMakeContentDirectories()
    {
        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/parent/foo-dest');

        $result = $this->publisher->publish();

        $pushVerifier = Phake::verify($this->git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('parent/foo-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->isolator)->is_dir('/tmp/woodhouse-10101/parent'),
            Phake::verify($this->isolator)->mkdir('/tmp/woodhouse-10101/parent', 0777, true),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/parent/foo-dest'),
            Phake::verify($this->git)->add('parent/foo-dest'),
            Phake::verify($this->git)->diff(true),
            Phake::verify($this->git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->git)->pull(),
            $pushVerifier,
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishToNewBranch()
    {
        Phake::when($this->git)
            ->cloneRepo(Phake::anyParameters())
            ->thenThrow(new RuntimeException('... test-branch not found in upstream origin ...'))
            ->thenReturn($this->cloneProcess);

        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');
        $this->publisher->add('/source/bar', '/bar-dest');

        $result = $this->publisher->publish();

        $pushVerifier = Phake::verify($this->git, Phake::times(2))->push('origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                null,
                1
            ),
            Phake::verify($this->git)->checkout('test-branch', true),
            Phake::verify($this->git)->remove('.'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->git)->add('bar-dest'),
            Phake::verify($this->git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->git)->commit('Test commit message.'),
            $pushVerifier,
            Phake::verify($this->git)->pull(),
            $pushVerifier,
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );

        $this->assertTrue($result);
    }

    public function testPublishSuccessPushAttemptsExhausted()
    {
        Phake::when($this->git)
            ->push(Phake::anyParameters())
            ->thenThrow(new RuntimeException)
            ->thenThrow(new RuntimeException)
            ->thenReturn(Phake::mock('Symfony\Component\Process\Process'));

        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');
        $this->publisher->add('/source/bar', '/bar-dest');

        $this->assertTrue($this->publisher->publish());

        $pushVerifier = Phake::verify($this->git, Phake::times(3))->push('origin', 'test-branch');
        $pullVerifier = Phake::verify($this->git, Phake::times(2))->pull();

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->git)->remove('bar-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->git)->add('bar-dest'),
            Phake::verify($this->git)->diff(true),
            Phake::verify($this->git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->git)->commit('Test commit message.'),
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailurePushAttemptsExhausted()
    {
        $exception = new RuntimeException;

        Phake::when($this->git)
            ->push(Phake::anyParameters())
            ->thenThrow($exception);

        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');
        $this->publisher->add('/source/bar', '/bar-dest');

        try {
            $this->publisher->publish();
        } catch (RuntimeException $e) {
            $this->assertSame($exception, $e);
        }

        $pushVerifier = Phake::verify($this->git, Phake::times(3))->push('origin', 'test-branch');
        $pullVerifier = Phake::verify($this->git, Phake::times(2))->pull();

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->git)->remove('bar-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->git)->add('bar-dest'),
            Phake::verify($this->git)->diff(true),
            Phake::verify($this->git)->setConfig('user.name', 'Woodhouse'),
            Phake::verify($this->git)->setConfig('user.email', 'contact@icecave.com.au'),
            Phake::verify($this->git)->commit('Test commit message.'),
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailureNoRepository()
    {
        $this->setExpectedException('RuntimeException', 'No repository set.');
        $this->publisher->publish();
    }

    public function testPublishCloneFailure()
    {
        $exception = new RuntimeException;

        Phake::when($this->git)
            ->cloneRepo(Phake::anyParameters())
            ->thenThrow($exception);

        $this->publisher->setRepository('foo/bar');

        try {
            $this->publisher->publish();
            $this->fail('Expected exception did not propagate.');
        } catch (RuntimeException $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function testDryRun()
    {
        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');
        $this->publisher->add('/source/bar', '/bar-dest');

        $result = $this->publisher->dryRun();

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->git)->remove('bar-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->fileSystem)->mirror('/source/bar/', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->git)->add('bar-dest'),
            Phake::verify($this->git)->diff(true),
            Phake::verify($this->fileSystem)->remove('/tmp/woodhouse-10101')
        );

        Phake::verify($this->git, Phake::never())->commit(Phake::anyParameters());
        Phake::verify($this->git, Phake::never())->push(Phake::anyParameters());

        $this->assertTrue($result);
    }

    public function testDryRunNoChanges()
    {
        Phake::when($this->diffProcess)
            ->getOutput()
            ->thenReturn('    '); // whitespace only

        $this->publisher->setRepository('foo/bar');
        $this->publisher->setCommitMessage('Test commit message.');
        $this->publisher->setBranch('test-branch');
        $this->publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->publisher->add('/source/foo', '/foo-dest');

        $result = $this->publisher->dryRun();

        Phake::inOrder(
            Phake::verify($this->git)->cloneRepo(
                '/tmp/woodhouse-10101',
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                'test-branch',
                1
            ),
            Phake::verify($this->git)->remove('foo-dest'),
            Phake::verify($this->isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->fileSystem)->copy('/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->git)->add('foo-dest'),
            Phake::verify($this->git)->diff(true)
        );

        Phake::verify($this->git, Phake::never())->commit(Phake::anyParameters());
        Phake::verify($this->git, Phake::never())->push(Phake::anyParameters());

        $this->assertFalse($result);
    }
}
