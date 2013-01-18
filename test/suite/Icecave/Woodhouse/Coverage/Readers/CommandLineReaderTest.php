<?php
namespace Icecave\Woodhouse\Coverage\Readers;

use PHPUnit_Framework_TestCase;

class CommandLineReaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_reader = new CommandLineReader('23.5');
    }

    public function testReadPercentage()
    {
        $this->assertSame(23.5, $this->_reader->readPercentage());
    }
}
