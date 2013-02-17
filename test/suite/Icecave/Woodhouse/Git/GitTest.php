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
        $this->_finder = Phake::mock('Symfony\Component\Process\ExecutableFinder');
        $this->_process = Phake::mock('Symfony\Component\Process\Process');

        Phake::when($this->_finder)
            ->find(Phake::anyParameters())
            ->thenReturn('/opt/local/bin/git');

        Phake::when($this->_process)
            ->isSuccessful()
            ->thenReturn(true);

        $this->_git = Phake::partialMock(
            __NAMESPACE__ . '\Git',
            $this->_finder
        );

        Phake::when($this->_git)
            ->createProcess(Phake::anyParameters())
            ->thenReturn($this->_process);
    }

    public function testConstructorDefaults()
    {
        $git = new Git;

        $this->assertContains('git', $this->_git->executable());
    }

    public function testConstructor()
    {
        Phake::verify($this->_finder)->find('git');

        $this->assertSame('/opt/local/bin/git', $this->_git->executable());
    }

    public function testCloneRepo()
    {
        $process = $this->_git->cloneRepo('/path/to/clone', 'git://url', 'develop', 100);

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'clone' 'git://url' '--branch' 'develop' '--depth' '100' '/path/to/clone'");

        $this->assertSame('/path/to/clone', Liberator::liberate($this->_git)->workingDirectory);
        $this->assertSame($this->_process, $process);
    }

    public function testCheckout()
    {
        $process = $this->_git->checkout('test-branch');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'checkout' 'test-branch'");

        $this->assertSame($this->_process, $process);
    }

    public function testCheckoutOrphan()
    {
        $process = $this->_git->checkout('test-branch', true);

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'checkout' '--orphan' 'test-branch'");

        $this->assertSame($this->_process, $process);
    }

    public function testAdd()
    {
        $process = $this->_git->add('foo');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'add' 'foo'");

        $this->assertSame($this->_process, $process);
    }

    public function testRemove()
    {
        $process = $this->_git->remove('foo');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'rm' '-rf' '--ignore-unmatch' 'foo'");

        $this->assertSame($this->_process, $process);
    }

    public function testDiff()
    {
        $process = $this->_git->diff();

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'diff'");

        $this->assertSame($this->_process, $process);
    }

    public function testDiffStagedFiles()
    {
        $process = $this->_git->diff(true);

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'diff' '--cached'");

        $this->assertSame($this->_process, $process);
    }

    public function testCommit()
    {
        $process = $this->_git->commit('Commit message!');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'commit' '-m' 'Commit message!'");

        $this->assertSame($this->_process, $process);
    }

    public function testPush()
    {
        $process = $this->_git->push('origin');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'push' 'origin'");

        $this->assertSame($this->_process, $process);
    }

    public function testPushWithExplicitBranch()
    {
        $process = $this->_git->push('origin', 'master');

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'push' 'origin' 'master'");

        $this->assertSame($this->_process, $process);
    }

    public function testPull()
    {
        $process = $this->_git->pull();

        Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'pull'");

        $this->assertSame($this->_process, $process);
    }

    public function testExecute()
    {
        Liberator::liberate($this->_git)->workingDirectory = '/path/to/clone';

        $result = $this->_git->execute(array('log', '-1'));

        Phake::inOrder(
            Phake::verify($this->_git)->createProcess(),
            Phake::verify($this->_process)->setCommandLine("/opt/local/bin/git 'log' '-1'"),
            Phake::verify($this->_process)->setWorkingDirectory('/path/to/clone'),
            Phake::verify($this->_process)->run()
        );

        $this->assertSame($this->_process, $result);
    }

    public function testExecuteFailure()
    {
        Phake::when($this->_process)
            ->isSuccessful()
            ->thenReturn(false);

        Phake::when($this->_process)
            ->getErrorOutput()
            ->thenReturn('This is the RESTRICTED error output.');

        $this->_git->setOutputFilter(
            function ($buffer) {
                return str_replace('RESTRICTED', 'HAPPY BUNNIES', $buffer);
            }
        );

        $expectedMessage  = 'Git command failed!' . PHP_EOL;
        $expectedMessage .= "Command Line: /opt/local/bin/git 'HAPPY BUNNIES'" . PHP_EOL;
        $expectedMessage .= PHP_EOL;
        $expectedMessage .= 'This is the HAPPY BUNNIES error output.';

        $this->setExpectedException('RuntimeException', $expectedMessage);
        $result = $this->_git->execute(array('RESTRICTED'));
    }

    public function testCreateProcess()
    {
        Phake::when($this->_git)
            ->createProcess()
            ->thenCallParent();

        $process = Liberator::liberate($this->_git)->createProcess();

        $this->assertInstanceOf('Symfony\Component\Process\Process', $process);
    }
}
