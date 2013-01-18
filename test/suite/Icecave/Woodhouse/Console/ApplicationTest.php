<?php
namespace Icecave\Woodhouse\Console;

use PHPUnit_Framework_TestCase;

class PlaceholderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_application = new Application;
    }

    public function testPlaceholder()
    {
        $this->assertTrue(true);
    }
}
