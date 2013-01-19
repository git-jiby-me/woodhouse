<?php
namespace Icecave\Woodhouse\Publisher;

use Phake;
use PHPUnit_Framework_TestCase;

class AbstractPublisherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_publisher = Phake::partialMock(__NAMESPACE__ . '\AbstractPublisher');
    }

    public function testConstruct()
    {
        $this->assertSame(array(), $this->_publisher->contentPaths());
    }

    public function testAdd()
    {
        $this->_publisher->add('foo', 'bar');
        $this->assertSame(array('foo' => 'bar'), $this->_publisher->contentPaths());

        $this->_publisher->add('spam', 'doom');
        $this->assertSame(array('foo' => 'bar', 'spam' => 'doom'), $this->_publisher->contentPaths());
    }

    public function testAddRemovesLeadingSlashes()
    {
        $this->_publisher->add('foo', '//bar');
        $this->assertSame(array('foo' => 'bar'), $this->_publisher->contentPaths());
    }

    public function testRemove()
    {
        $this->_publisher->add('spam', 'doom');
        $this->_publisher->add('foo', 'bar');
        $this->_publisher->remove('spam');
        $this->assertSame(array('foo' => 'bar'), $this->_publisher->contentPaths());
    }

    public function testClear()
    {
        $this->_publisher->add('foo', 'bar');
        $this->_publisher->add('spam', 'doom');
        $this->_publisher->clear();
        $this->assertSame(array(), $this->_publisher->contentPaths());
    }
}
