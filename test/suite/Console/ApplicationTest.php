<?php

namespace Icecave\Woodhouse\Console;

use Phake;
use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = Phake::partialMock(__NAMESPACE__ . '\Application', '/path/to/vendors');
    }

    public function testConstructor()
    {
        $this->assertSame('Woodhouse', $this->application->getName());
        $this->assertSame('1.0.0', $this->application->getVersion());
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

        $this->assertSame($expected, array_keys($this->application->all()));
    }

    public function testHelpers()
    {
        $this->assertTrue($this->application->getHelperSet()->has('hidden-input'));
    }

    public function testVendorPath()
    {
        $this->assertSame('/path/to/vendors', $this->application->vendorPath());
    }
}
