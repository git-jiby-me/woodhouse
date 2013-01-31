<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Exception;
use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Phake;
use PHPUnit_Framework_TestCase;

class JUnitReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_path = '/path/to/report.xml';
        $this->_reader = new JUnitReader($this->_path, $this->_isolator);
    }

    public function setupContentFixture($content)
    {
        Phake::when($this->_isolator)
            ->simplexml_load_file($this->_path)
            ->thenReturn(simplexml_load_string($content));
    }

    public function testReadStatus()
    {
        $content = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="" tests="15" assertions="53" failures="0" errors="0" time="0.163155">
    <testsuite name="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" namespace="Icecave\Woodhouse\Console\Command\GitHub" fullPackage="Icecave.Woodhouse.Console.Command.GitHub" tests="15" assertions="53" failures="0" errors="0" time="0.163155">
      <testcase name="testExecuteWithAuthTokenEnv" class="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" line="126" assertions="6" time="0.010905"/>
    </testsuite>
  </testsuite>
</testsuites>
EOD;
        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::PASSING(), $this->_reader->readStatus());
    }

    public function testReadStatusError()
    {
        $content = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="" tests="15" assertions="53" failures="0" errors="1" time="0.163155">
    <testsuite name="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" namespace="Icecave\Woodhouse\Console\Command\GitHub" fullPackage="Icecave.Woodhouse.Console.Command.GitHub" tests="15" assertions="53" failures="0" errors="0" time="0.163155">
      <testcase name="testExecuteWithAuthTokenEnv" class="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" line="126" assertions="6" time="0.010905"/>
    </testsuite>
  </testsuite>
</testsuites>
EOD;

        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->_reader->readStatus());
    }

    public function testReadStatusFail()
    {
        $content = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<testsuites>
  <testsuite name="" tests="15" assertions="53" failures="1" errors="0" time="0.163155">
    <testsuite name="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" namespace="Icecave\Woodhouse\Console\Command\GitHub" fullPackage="Icecave.Woodhouse.Console.Command.GitHub" tests="15" assertions="53" failures="0" errors="0" time="0.163155">
      <testcase name="testExecuteWithAuthTokenEnv" class="Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest" file="/Users/james/Documents/Development/icecave/woodhouse/test/suite/Icecave/Woodhouse/Console/Command/GitHub/PublishCommandTest.php" line="126" assertions="6" time="0.010905"/>
    </testsuite>
  </testsuite>
</testsuites>
EOD;

        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->_reader->readStatus());
    }

    public function testReadStatusFailure()
    {
        Phake::when($this->_isolator)
            ->simplexml_load_file($this->_path)
            ->thenThrow(new Exception);

        $this->setExpectedException('RuntimeException', 'Unable to parse JUnit test report.');
        $this->_reader->readStatus();
    }
}
