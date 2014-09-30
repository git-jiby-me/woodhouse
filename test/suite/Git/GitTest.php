<?php
namespace Icecave\Woodhouse\Git;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;
use Phake;
use RuntimeException;

class GitTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->finder = Phake::mock('Symfony\Component\Process\ExecutableFinder');
        $this->process = Phake::mock('Symfony\Component\Process\Process');

        Phake::when($this->finder)
            ->find(Phake::anyParameters())
            ->thenReturn('/opt/local/bin/git');

        Phake::when($this->process)
            ->isSuccessful()
            ->thenReturn(true);

        $this->git = Phake::partialMock(
            __NAMESPACE__ . '\Git',
            $this->finder
        );

        Phake::when($this->git)
            ->createProcess(Phake::anyParameters())
            ->thenReturn($this->process);
    }

    public function testConstructorDefaults()
    {
        $git = new Git();

        $this->assertContains('git', $this->git->executable());
    }

    public function testConstructor()
    {
        Phake::verify($this->finder)->find('git');

        $this->assertSame('/opt/local/bin/git', $this->git->executable());
    }

    public function testCloneRepo()
    {
        $process = $this->git->cloneRepo('/path/to/clone', 'git://url');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'clone' 'git://url' '/path/to/clone'");

        $this->assertSame('/path/to/clone', Liberator::liberate($this->git)->workingDirectory);
        $this->assertSame($this->process, $process);
    }

    public function testCloneRepoWithExplicitBranch()
    {
        $process = $this->git->cloneRepo('/path/to/clone', 'git://url', 'develop', 100);

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'clone' 'git://url' '--branch' 'develop' '--depth' '100' '/path/to/clone'");

        $this->assertSame('/path/to/clone', Liberator::liberate($this->git)->workingDirectory);
        $this->assertSame($this->process, $process);
    }

    public function testCheckout()
    {
        $process = $this->git->checkout('test-branch');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'checkout' 'test-branch'");

        $this->assertSame($this->process, $process);
    }

    public function testCheckoutOrphan()
    {
        $process = $this->git->checkout('test-branch', true);

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'checkout' '--orphan' 'test-branch'");

        $this->assertSame($this->process, $process);
    }

    public function testAdd()
    {
        $process = $this->git->add('foo');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'add' '--force' 'foo'");

        $this->assertSame($this->process, $process);
    }

    public function testRemove()
    {
        $process = $this->git->remove('foo');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'rm' '-rf' '--ignore-unmatch' 'foo'");

        $this->assertSame($this->process, $process);
    }

    public function testDiff()
    {
        $process = $this->git->diff();

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'diff'");

        $this->assertSame($this->process, $process);
    }

    public function testDiffStagedFiles()
    {
        $process = $this->git->diff(true);

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'diff' '--cached'");

        $this->assertSame($this->process, $process);
    }

    public function testCommit()
    {
        $process = $this->git->commit('Commit message!');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'commit' '-m' 'Commit message!'");

        $this->assertSame($this->process, $process);
    }

    public function testPush()
    {
        $process = $this->git->push('origin');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'push' 'origin'");

        $this->assertSame($this->process, $process);
    }

    public function testPushWithExplicitBranch()
    {
        $process = $this->git->push('origin', 'master');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'push' 'origin' 'master'");

        $this->assertSame($this->process, $process);
    }

    public function testPull()
    {
        $process = $this->git->pull();

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'pull'");

        $this->assertSame($this->process, $process);
    }

    public function testSetConfig()
    {
        $process = $this->git->setConfig('key', 'value');

        Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'config' 'key' 'value'");

        $this->assertSame($this->process, $process);
    }

    public function testExecute()
    {
        Liberator::liberate($this->git)->workingDirectory = '/path/to/clone';

        $result = $this->git->execute(array('log', '-1'));

        Phake::inOrder(
            Phake::verify($this->git)->createProcess(),
            Phake::verify($this->process)->setCommandLine("/opt/local/bin/git 'log' '-1'"),
            Phake::verify($this->process)->setWorkingDirectory('/path/to/clone'),
            Phake::verify($this->process)->run()
        );

        $this->assertSame($this->process, $result);
    }

    public function testExecuteFailure()
    {
        Phake::when($this->process)
            ->isSuccessful()
            ->thenReturn(false);

        Phake::when($this->process)
            ->getErrorOutput()
            ->thenReturn('This is the RESTRICTED error output.');

        $this->git->setOutputFilter(
            function ($buffer) {
                return str_replace('RESTRICTED', 'HAPPY BUNNIES', $buffer);
            }
        );

        $expectedMessage  = 'Git command failed!' . PHP_EOL;
        $expectedMessage .= "Command Line: /opt/local/bin/git 'HAPPY BUNNIES'" . PHP_EOL;
        $expectedMessage .= PHP_EOL;
        $expectedMessage .= 'This is the HAPPY BUNNIES error output.';

        $this->setExpectedException('RuntimeException', $expectedMessage);
        $result = $this->git->execute(array('RESTRICTED'));
    }

    public function testCreateProcess()
    {
        Phake::when($this->git)
            ->createProcess()
            ->thenCallParent();

        $process = Liberator::liberate($this->git)->createProcess();

        $this->assertInstanceOf('Symfony\Component\Process\Process', $process);
    }
}
