<?php
namespace Icecave\Woodhouse\Console\Command\GitHub;

use Icecave\Woodhouse\Console\Application;
use PHPUnit_Framework_TestCase;
use Phake;
use Symfony\Component\Console\Input\StringInput;

class GetTokenCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_application = new Application('/path/to/vendors');

        $this->_command = new GetTokenCommand;
        $this->_command->setApplication($this->_application);

        $this->_output = Phake::mock('Symfony\Component\Console\Output\OutputInterface');
    }

    public function testConfigure()
    {
        $this->assertSame('github:get-token', $this->_command->getName());
        $this->assertSame('Create (or get an existing) GitHub API token for Woodhouse.', $this->_command->getDescription());
    }
}
