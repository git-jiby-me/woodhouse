<?php
namespace Icecave\Woodhouse;

use PHPUnit_Framework_TestCase;

class PlaceholderTest extends PHPUnit_Framework_TestCase
{
    public function testPlaceholder()
    {
        $placeHolder = new Placeholder;
        $this->assertTrue($placeHolder->test());
    }
}
