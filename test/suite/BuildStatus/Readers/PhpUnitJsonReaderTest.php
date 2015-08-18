<?php

namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Phake;
use PHPUnit_Framework_TestCase;

class PhpUnitJsonReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->isolator = Phake::partialMock('Icecave\Isolator\Isolator');
        $this->path = '/path/to/report.json';
        $this->reader = new PhpUnitJsonReader($this->path, null, $this->isolator);
    }

    public function setupContentFixture($content)
    {
        $stream = fopen('data://text/plain;base64,' . base64_encode($content), 'rb');

        Phake::when($this->isolator)
            ->fopen($this->path, 'r')
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
        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::PASSING(), $this->reader->readStatus());
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

        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->reader->readStatus());
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

        $this->setupContentFixture($content);

        $this->assertSame(BuildStatus::FAILING(), $this->reader->readStatus());
    }

    public function testReadStatusFailure()
    {
        $this->setupContentFixture('<invalid content>');

        $this->setExpectedException('RuntimeException', 'Unable to parse PHPUnit test report.');
        $this->reader->readStatus();
    }
}
