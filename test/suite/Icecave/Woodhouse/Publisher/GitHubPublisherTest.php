<?php
namespace Icecave\Woodhouse\Publisher;

use Phake;
use PHPUnit_Framework_TestCase;

class GitHubPublisherTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_publisher = new GitHubPublisher($this->_isolator);
    }

    public function testRepository()
    {
        $this->assertNull($this->_publisher->repository());
    }

    public function testSetRepository()
    {
        $this->_publisher->setRepository('foo/bar');
        $this->assertSame('foo/bar', $this->_publisher->repository());
    }

    public function testSetRepositoryFailure()
    {
        $this->assertNull($this->_publisher->repository());

        $this->setExpectedException('InvalidArgumentException', 'Invalid repository name: "foo:bar".');
        $this->_publisher->setRepository('foo:bar');
    }

    public function testBranch()
    {
        $this->assertSame('gh-pages', $this->_publisher->branch());
    }

    public function testSetBranch()
    {
        $this->_publisher->setBranch('some-branch');
        $this->assertSame('some-branch', $this->_publisher->branch());
    }

    public function testAuthToken()
    {
        $this->assertNull($this->_publisher->authToken());
    }

    public function testSetAuthToken()
    {
        $token = sha1('foo');
        $this->_publisher->setAuthToken($token);
        $this->assertSame($token, $this->_publisher->authToken());
    }

    public function testSetAuthTokenFailure()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid authentication token.');
        $this->_publisher->setAuthToken('foo');
    }
}
