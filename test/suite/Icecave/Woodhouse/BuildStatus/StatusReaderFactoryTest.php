<?php
namespace Icecave\Woodhouse\BuildStatus;

use Phake;
use PHPUnit_Framework_TestCase;
use Eloquent\Liberator\Liberator;

class StatusReaderFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_factory = new StatusReaderFactory($this->_isolator);
    }

    public function testSupportedTypes()
    {
        $expected = array(
            'junit',
            'phpunit',
            'tap',
        );

        $this->assertSame($expected, $this->_factory->supportedTypes());
    }

    public function testCreateJUnit()
    {
        $reader = $this->_factory->create('junit', '/path/to/report.xml');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\JUnitReader', $reader);
        $this->assertSame('/path/to/report.xml', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->_isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreatePhpUnit()
    {
        $reader = $this->_factory->create('phpunit', '/path/to/report.json');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\PhpUnitJsonReader', $reader);
        $this->assertSame('/path/to/report.json', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->_isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreateTap()
    {
        $reader = $this->_factory->create('tap', '/path/to/report.tap');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\TapReader', $reader);
        $this->assertSame('/path/to/report.tap', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->_isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreateFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown reader type: "garbage".');
        $this->_factory->create('garbage', '');
    }
}
