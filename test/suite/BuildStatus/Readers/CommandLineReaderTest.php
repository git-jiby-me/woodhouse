<?php

namespace Icecave\Woodhouse\BuildStatus\Readers;

use Icecave\Woodhouse\BuildStatus\BuildStatus;
use PHPUnit_Framework_TestCase;

class CommandLineReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->reader = new CommandLineReader('passing');
    }

    public function testReadPercentage()
    {
        $this->assertSame(BuildStatus::PASSING(), $this->reader->readStatus());
    }
}
