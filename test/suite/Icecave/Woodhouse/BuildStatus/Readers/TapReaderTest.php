<?php
namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use Phake;
use PHPUnit_Framework_TestCase;

class TapReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_path = '/path/to/report.tap';
        $this->_reader = new TapReader($this->_path, $this->_isolator);
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

        Phake::when($this->_isolator)
            ->file_get_contents($this->_path)
            ->thenReturn($content);

        $this->assertSame(BuildStatus::PASSING(), $this->_reader->readStatus());
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

        Phake::when($this->_isolator)
            ->file_get_contents($this->_path)
            ->thenReturn($content);

        $this->assertSame(BuildStatus::FAILING(), $this->_reader->readStatus());
    }

    public function testReadStatusFailure()
    {
        Phake::when($this->_isolator)
            ->file_get_contents($this->_path)
            ->thenReturn('<invalid content>');

        $this->setExpectedException('RuntimeException', 'Unable to parse TAP test report.');
        $this->_reader->readStatus();
    }
}
