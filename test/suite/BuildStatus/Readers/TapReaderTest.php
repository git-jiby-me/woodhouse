<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Phake;
use PHPUnit_Framework_TestCase;

class TapReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->path = '/path/to/report.tap';
        $this->reader = new TapReader($this->path, $this->isolator);
    }

    public function testReadStatus()
    {
        $content = <<<EOD
TAP version 13
ok 1 - Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testConfigure
ok 2 - Error: Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testConstructorDefaults
ok 3 - Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testExecute
1..3
EOD;

        Phake::when($this->isolator)
            ->file_get_contents($this->path)
            ->thenReturn($content);

        $this->assertSame(BuildStatus::PASSING(), $this->reader->readStatus());
    }

    public function testReadStatusFailing()
    {
        $content = <<<EOD
TAP version 13
ok 1 - Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testConfigure
not ok 2 - Error: Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testConstructorDefaults
ok 3 - Icecave\Woodhouse\Console\Command\GitHub\PublishCommandTest::testExecute
1..3
EOD;

        Phake::when($this->isolator)
            ->file_get_contents($this->path)
            ->thenReturn($content);

        $this->assertSame(BuildStatus::FAILING(), $this->reader->readStatus());
    }

    public function testReadStatusFailure()
    {
        Phake::when($this->isolator)
            ->file_get_contents($this->path)
            ->thenReturn('<invalid content>');

        $this->setExpectedException('RuntimeException', 'Unable to parse TAP test report.');
        $this->reader->readStatus();
    }
}
