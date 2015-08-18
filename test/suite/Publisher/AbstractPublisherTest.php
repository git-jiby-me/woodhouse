<?php

namespace Icecave\Woodhouse\Publisher;

use Phake;
use PHPUnit_Framework_TestCase;

class AbstractPublisherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->publisher = Phake::partialMock(__NAMESPACE__ . '\AbstractPublisher');
    }

    public function testConstruct()
    {
        $this->assertSame(array(), $this->publisher->contentPaths());
    }

    public function testAdd()
    {
        $this->publisher->add('foo', 'bar');
        $this->assertSame(array('foo' => 'bar'), $this->publisher->contentPaths());

        $this->publisher->add('spam', 'doom');
        $this->assertSame(array('foo' => 'bar', 'spam' => 'doom'), $this->publisher->contentPaths());
    }

    public function testAddRemovesLeadingSlashes()
    {
        $this->publisher->add('foo', '//bar');
        $this->assertSame(array('foo' => 'bar'), $this->publisher->contentPaths());
    }

    public function testRemove()
    {
        $this->publisher->add('spam', 'doom');
        $this->publisher->add('foo', 'bar');
        $this->publisher->remove('spam');
        $this->assertSame(array('foo' => 'bar'), $this->publisher->contentPaths());
    }

    public function testClear()
    {
        $this->publisher->add('foo', 'bar');
        $this->publisher->add('spam', 'doom');
        $this->publisher->clear();
        $this->assertSame(array(), $this->publisher->contentPaths());
    }
}
