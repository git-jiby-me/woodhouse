<?php

namespace Icecave\Woodhouse\BuildStatus;

use Eloquent\Liberator\Liberator;
use Phake;
use PHPUnit_Framework_TestCase;

class StatusReaderFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->factory = new StatusReaderFactory($this->isolator);
    }

    public function testSupportedTypes()
    {
        $expected = array(
            'junit',
            'phpunit',
            'result',
            'tap',
        );

        $this->assertSame($expected, $this->factory->supportedTypes());
    }

    public function testCreateJUnit()
    {
        $reader = $this->factory->create('junit', '/path/to/report.xml');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\JUnitReader', $reader);
        $this->assertSame('/path/to/report.xml', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreatePhpUnit()
    {
        $reader = $this->factory->create('phpunit', '/path/to/report.json');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\PhpUnitJsonReader', $reader);
        $this->assertSame('/path/to/report.json', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreateCommandLine()
    {
        $reader = $this->factory->create('result', 'passing');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\CommandLineReader', $reader);
        $this->assertSame('passing', Liberator::liberate($reader)->buildStatus);
    }

    public function testCreateTap()
    {
        $reader = $this->factory->create('tap', '/path/to/report.tap');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\TapReader', $reader);
        $this->assertSame('/path/to/report.tap', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreateFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown reader type: "garbage".');
        $this->factory->create('garbage', '');
    }
}
