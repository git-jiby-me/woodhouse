<?php
namespace Icecave\Woodhouse\Console\Command;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PublishCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_command = new PublishCommand;
        $this->_tester = new CommandTester($this->_command);
    }

    public function testName()
    {
        $this->assertSame('publish', $this->_command->getName());
    }
}
