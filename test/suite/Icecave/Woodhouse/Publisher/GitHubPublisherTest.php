<?php
namespace Icecave\Woodhouse\Publisher;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;
use Phake;
use RuntimeException;
use Icecave\Isolator\Isolator;

class GitHubPublisherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock(get_class(Isolator::get()));

        Phake::when($this->_isolator)
            ->sys_get_temp_dir()
            ->thenReturn('/tmp');

        Phake::when($this->_isolator)
            ->getmypid()
            ->thenReturn('10101');

        $this->_publisher = Phake::partialMock(__NAMESPACE__ . '\GitHubPublisher', $this->_isolator);
    }

    public function testPublish()
    {
        Phake::when($this->_publisher)
            ->execute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute('git', 'push', 'origin', 'test-branch')
            ->thenReturn(null)
            ->thenReturn('');

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $this->_publisher->publish();

        $pushVerifier = Phake::verify($this->_publisher, Phake::times(2))->tryExecute('git', 'push', 'origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->_publisher)->execute(
                'git', 'clone', '--quiet',
                '--branch', 'test-branch',
                '--depth', 0,
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                '/tmp/woodhouse-10101'
            ),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', 'foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', 'bar-dest'),
            Phake::verify($this->_publisher)->execute('cp', '/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'foo-dest'),
            Phake::verify($this->_publisher)->execute('cp', '/source/bar', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'commit', '-m', 'Test commit message.'),
            $pushVerifier,
            Phake::verify($this->_publisher)->execute('git', 'pull'),
            $pushVerifier,
            Phake::verify($this->_publisher)->execute('rm', '-rf', '/tmp/woodhouse-10101')
        );
    }

    public function testPublishDirectoryCopyFlags()
    {
        Phake::when($this->_publisher)
            ->execute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute('git', 'push', 'origin', 'test-branch')
            ->thenReturn(null)
            ->thenReturn('');

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->add('/source/foo', '/foo-dest');

        Phake::when($this->_isolator)
            ->is_dir('/source/foo')
            ->thenReturn(true);

        Phake::when($this->_isolator)
            ->php_uname('s')
            ->thenReturn('Linux')
            ->thenReturn('Darwin')
            ->thenReturn('FreeBSD')
            ->thenReturn('Other');

        $this->_publisher->publish();
        $this->_publisher->publish();
        $this->_publisher->publish();
        $this->_publisher->publish();

        $bsdVerifier = Phake::verify($this->_publisher, Phake::times(2))->execute('cp', '-R', '/source/foo/', '/tmp/woodhouse-10101/foo-dest');
        $isDirVerifier = Phake::verify($this->_isolator, Phake::times(4))->is_dir('/source/foo');

        Phake::inOrder(
            $isDirVerifier,
            Phake::verify($this->_publisher)->execute('cp', '-rT', '/source/foo/', '/tmp/woodhouse-10101/foo-dest'),
            $isDirVerifier,
            $bsdVerifier,
            $isDirVerifier,
            $bsdVerifier,
            $isDirVerifier,
            Phake::verify($this->_publisher)->execute('cp', '-r', '/source/foo/', '/tmp/woodhouse-10101/foo-dest')
        );
    }

    public function testPublishMakeContentDirectories()
    {
        Phake::when($this->_publisher)
            ->execute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute('git', 'push', 'origin', 'test-branch')
            ->thenReturn(null)
            ->thenReturn('');

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/parent/foo-dest');

        $this->_publisher->publish();

        $pushVerifier = Phake::verify($this->_publisher, Phake::times(2))->tryExecute('git', 'push', 'origin', 'test-branch');

        Phake::inOrder(
            Phake::verify($this->_publisher)->execute(
                'git', 'clone', '--quiet',
                '--branch', 'test-branch',
                '--depth', 0,
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                '/tmp/woodhouse-10101'
            ),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', 'parent/foo-dest'),
            Phake::verify($this->_isolator)->is_dir('/tmp/woodhouse-10101/parent'),
            Phake::verify($this->_isolator)->mkdir('/tmp/woodhouse-10101/parent', 0777, true),
            Phake::verify($this->_publisher)->execute('cp', '/source/foo', '/tmp/woodhouse-10101/parent/foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'parent/foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'commit', '-m', 'Test commit message.'),
            $pushVerifier,
            Phake::verify($this->_publisher)->execute('git', 'pull'),
            $pushVerifier,
            Phake::verify($this->_publisher)->execute('rm', '-rf', '/tmp/woodhouse-10101')
        );
    }

    public function testPublishToNewBranch()
    {
        Phake::when($this->_publisher)
            ->execute(Phake::anyParameters())
            ->thenReturn('... test-branch not found in upstream origin ...');

        Phake::when($this->_publisher)
            ->tryExecute(Phake::anyParameters())
            ->thenReturn('');

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        $this->_publisher->publish();

        Phake::inOrder(
            Phake::verify($this->_publisher)->execute(
                'git', 'clone', '--quiet',
                '--branch', 'test-branch',
                '--depth', 0,
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                '/tmp/woodhouse-10101'
            ),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_publisher)->execute('git', 'checkout', '--orphan', 'test-branch'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', '.'),
            Phake::verify($this->_publisher)->execute('cp', '/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'foo-dest'),
            Phake::verify($this->_publisher)->execute('cp', '/source/bar', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'commit', '-m', 'Test commit message.'),
            Phake::verify($this->_publisher)->tryExecute('git', 'push', 'origin', 'test-branch'),
            Phake::verify($this->_publisher)->execute('rm', '-rf', '/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailurePushAttemptsExhausted()
    {
        Phake::when($this->_publisher)
            ->execute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute(Phake::anyParameters())
            ->thenReturn('');

        Phake::when($this->_publisher)
            ->tryExecute('git', 'push', 'origin', 'test-branch')
            ->thenReturn(null);

        $this->_publisher->setRepository('foo/bar');
        $this->_publisher->setCommitMessage('Test commit message.');
        $this->_publisher->setBranch('test-branch');
        $this->_publisher->setAuthToken('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33');

        $this->_publisher->add('/source/foo', '/foo-dest');
        $this->_publisher->add('/source/bar', '/bar-dest');

        try {
            $this->_publisher->publish();
        } catch (RuntimeException $e) {
            $this->assertSame('Unable to publish content.', $e->getMessage());
        }

        $pushVerifier = Phake::verify($this->_publisher, Phake::times(3))->tryExecute('git', 'push', 'origin', 'test-branch');
        $pullVerifier = Phake::verify($this->_publisher, Phake::times(2))->execute('git', 'pull');

        Phake::inOrder(
            Phake::verify($this->_publisher)->execute(
                'git', 'clone', '--quiet',
                '--branch', 'test-branch',
                '--depth', 0,
                'https://0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33:x-oauth-basic@github.com/foo/bar.git',
                '/tmp/woodhouse-10101'
            ),
            Phake::verify($this->_isolator)->chdir('/tmp/woodhouse-10101'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', 'foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'rm', '-rf', '--ignore-unmatch', 'bar-dest'),
            Phake::verify($this->_publisher)->execute('cp', '/source/foo', '/tmp/woodhouse-10101/foo-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'foo-dest'),
            Phake::verify($this->_publisher)->execute('cp', '/source/bar', '/tmp/woodhouse-10101/bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'add', 'bar-dest'),
            Phake::verify($this->_publisher)->execute('git', 'commit', '-m', 'Test commit message.'),
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            $pullVerifier,
            $pushVerifier,
            Phake::verify($this->_publisher)->execute('rm', '-rf', '/tmp/woodhouse-10101')
        );
    }

    public function testPublishFailureNoRepository()
    {
        $this->setExpectedException('RuntimeException', 'No repository set.');
        $this->_publisher->publish();
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

    public function testExecute()
    {
        Phake::when($this->_isolator)
            ->exec(
                $this->anything(),
                Phake::setReference(array('1', '2', '3')),
                Phake::setReference(0)
            )
            ->thenReturn('3');

        $liberator = Liberator::liberate($this->_publisher);
        $result = $liberator->execute('ls', '-la');
        $this->assertSame(implode(PHP_EOL, array('1', '2', '3')), $result);

        Phake::verify($this->_isolator)->exec("/usr/bin/env 'ls' '-la' 2>&1", array(), null);
    }

    public function testExecuteFailure()
    {
        Phake::when($this->_isolator)
            ->exec(
                $this->anything(),
                Phake::setReference(array('1', '2', '3')),
                Phake::setReference(1)
            )
            ->thenReturn('3');

        $liberator = Liberator::liberate($this->_publisher);

        $this->setExpectedException('RuntimeException', 'Failed executing command: "ls".');
        $result = $liberator->execute('ls', '-la');
    }

    public function testTryExecute()
    {
        Phake::when($this->_isolator)
            ->exec(
                $this->anything(),
                Phake::setReference(array('1', '2', '3')),
                Phake::setReference(0)
            )
            ->thenReturn('3');

        $liberator = Liberator::liberate($this->_publisher);
        $result = $liberator->tryExecute('ls', '-la');
        $this->assertSame(implode(PHP_EOL, array('1', '2', '3')), $result);

        Phake::verify($this->_isolator)->exec("/usr/bin/env 'ls' '-la' 2>&1", array(), null);
    }

    public function testTryExecuteFailure()
    {
        Phake::when($this->_isolator)
            ->exec(
                $this->anything(),
                Phake::setReference(array('1', '2', '3')),
                Phake::setReference(1)
            )
            ->thenReturn('3');

        $liberator = Liberator::liberate($this->_publisher);

        $liberator = Liberator::liberate($this->_publisher);
        $result = $liberator->tryExecute('ls', '-la');
        $this->assertNull($result);

        Phake::verify($this->_isolator)->exec("/usr/bin/env 'ls' '-la' 2>&1", array(), null);
    }
}
