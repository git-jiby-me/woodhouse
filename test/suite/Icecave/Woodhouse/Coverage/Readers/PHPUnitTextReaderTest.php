<?php
namespace Icecave\Woodhouse\Coverage\Readers;

use Phake;
use PHPUnit_Framework_TestCase;

class PHPUnitTestReader extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_path = '/path/to/report.txt';
        $this->_content = 'Code Coverage Report 
          2013-01-19 09:21:37

         Summary: 
          Classes: 42.86% (3/7)
          Methods: 47.62% (10/21)
          Lines:   52.98% (89/168)

        \Icecave\Woodhouse\Console\Command::PublishCommand
          Methods:  66.67% ( 2/ 3)   Lines:  60.38% ( 64/106)
        \Icecave\Woodhouse\Coverage::CoverageImageSelector
          Methods: 100.00% ( 3/ 3)   Lines: 100.00% (  8/  8)
        \Icecave\Woodhouse\Coverage::CoverageReaderFactory
          Methods: 100.00% ( 3/ 3)   Lines: 100.00% ( 12/ 12)
        \Icecave\Woodhouse\Coverage\Readers::CommandLineReader
          Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  5/  5)';

        Phake::when($this->_isolator)
            ->file_get_contents($this->_path)
            ->thenReturn($this->_content);

        $this->_reader = new PHPUnitTextReader($this->_path, $this->_isolator);
    }

    public function testReadPercentage()
    {
        $this->assertSame(52.98, $this->_reader->readPercentage());
    }

    public function testReadPercentageFailure()
    {
        Phake::when($this->_isolator)
            ->file_get_contents($this->_path)
            ->thenReturn('<invalid content>');

        $this->setExpectedException('RuntimeException', 'Unable to parse PHPUnit coverage report.');
        $this->_reader->readPercentage();
    }
}
