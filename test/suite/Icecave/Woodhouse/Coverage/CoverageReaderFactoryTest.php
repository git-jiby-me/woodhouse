<?php
namespace Icecave\Woodhouse\Coverage;

use Phake;
use PHPUnit_Framework_TestCase;
use Eloquent\Liberator\Liberator;

class CoverageReaderFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_factory = new CoverageReaderFactory($this->_isolator);
    }

    public function testSupportedTypes()
    {
        $expected = array(
            'percentage',
            'phpunit',
        );

        $this->assertSame($expected, $this->_factory->supportedTypes());
    }

    public function testCreatePhpUnit()
    {
        $reader = $this->_factory->create('phpunit', '/path/to/report.txt');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\PhpUnitTextReader', $reader);
        $this->assertSame('/path/to/report.txt', Liberator::liberate($reader)->reportPath);
        $this->assertSame($this->_isolator, Liberator::liberate($reader)->isolator);
    }

    public function testCreateCommandLine()
    {
        $reader = $this->_factory->create('percentage', '23.5');

        $this->assertInstanceOf(__NAMESPACE__ . '\Readers\CommandLineReader', $reader);
        $this->assertSame(23.5, $reader->readPercentage());
    }

    public function testCreateFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown reader type: "garbage".');
        $this->_factory->create('garbage', '');
    }
}
