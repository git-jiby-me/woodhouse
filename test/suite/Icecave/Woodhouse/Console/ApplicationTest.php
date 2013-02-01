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
        $this->assertSame('0.3.1', $this->_application->getVersion());

        Phake::verify($this->_application)->add($this->isInstanceOf(__NAMESPACE__ . '\Command\PublishCommand'));
    }

    public function testVendorPath()
    {
        $this->assertSame('/path/to/vendors', $this->_application->vendorPath());
    }
}
