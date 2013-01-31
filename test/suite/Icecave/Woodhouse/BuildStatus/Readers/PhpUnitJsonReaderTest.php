<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Phake;
use PHPUnit_Framework_TestCase;

class PhpUnitJsonReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_path = '/path/to/report.json';
        $this->_reader = new PhpUnitJsonReader($this->_path, $this->_isolator);

        Phake::when($this->_isolator)
            ->fgets(Phake::anyParameters())
            ->thenCallParent();

        Phake::when($this->_isolator)
            ->fclose(Phake::anyParameters())
            ->thenCallParent();
    }

    protected function setupStreamFixture($content)
    {
        $stream = fopen('data://text/plain;base64,' . base64_encode($content), 'rb');

        Phake::when($this->_isolator)
            ->fopen($this->_path, 'r')
            ->thenReturn($stream);
    }

    public function testReadStatus()
    {
        $content = <<<EOD
{
    "event": "testStart",
    "suite": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest",
    "test": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest::testExecuteWithFixedWidthCoverageImage"
}{
    "event": "test",
    "suite": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest",
    "test": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest::testExecuteWithFixedWidthCoverageImage",
    "status": "pass",
    "time": 0.012110948562622,
    "trace": [
    ],
    "message": "",
    "output": ""
}
EOD;
        $this->setupStreamFixture($content);

        $this->assertSame(BuildStatus::PASSING(), $this->_reader->readStatus());
    }

    public function testReadStatusError()
    {
        $content = <<<EOD
{
    "event": "test",
    "suite": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest",
    "test": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest::testConstructorDefaults",
    "status": "error",
    "time": 0.02892804145813,
    "trace": [
    ],
    "message": "include(error.php): failed to open stream: No such file or directory",
    "output": ""
}
EOD;

        $this->setupStreamFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->_reader->readStatus());
    }

    public function testReadStatusFail()
    {
        $content = <<<EOD
{
    "event": "test",
    "suite": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest",
    "test": "Icecave\\\\Woodhouse\\\\Console\\\\Command\\\\GitHub\\\\PublishCommandTest::testExecuteFailureMultipleCoveragePercentages",
    "status": "fail",
    "time": 0.0071730613708496,
    "trace": [

    ],
    "message": "This is a failure.",
    "output": ""
}
EOD;

    $this->setupStreamFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->_reader->readStatus());
    }

    public function testReadStatusFailure()
    {
        $this->setupStreamFixture('<invalid content>');

        $this->setExpectedException('RuntimeException', 'Unable to parse PHPUnit test report.');
        $this->_reader->readStatus();
    }
}
