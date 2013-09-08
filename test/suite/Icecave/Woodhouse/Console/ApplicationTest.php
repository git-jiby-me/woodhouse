<?php
namespace Icecave\Woodhouse\Console;

use Phake;
use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_application = Phake::partialMock(__NAMESPACE__ . '\Application', '/path/to/vendors');
    }

    public function testConstructor()
    {
        $this->assertSame('Woodhouse', $this->_application->getName());
        $this->assertSame('0.5.0', $this->_application->getVersion());
    }

    public function testCommands()
    {
        $expected = array(
            'help',
            'list',
            'github:create-auth',
            'github:delete-auth',
            'github:list-auth',
            'publish',
        );

        $this->assertSame($expected, array_keys($this->_application->all()));
    }

    public function testHelpers()
    {
        $this->assertTrue($this->_application->getHelperSet()->has('hidden-input'));
    }

    public function testVendorPath()
    {
        $this->assertSame('/path/to/vendors', $this->_application->vendorPath());
    }
}
