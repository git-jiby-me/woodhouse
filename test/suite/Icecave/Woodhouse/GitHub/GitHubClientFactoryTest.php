<?php
namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\BasicAuthListener;
use PHPUnit_Framework_TestCase;

class GitHubClientFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('Requires cURL extension.');
        }

        $this->_factory = new GitHubClientFactory;

        $expectedCACertPath = __DIR__;
        for ($i = 0; $i < 5; $i ++) {
            $expectedCACertPath = dirname($expectedCACertPath);
        }
        $expectedCACertPath .= '/lib/Icecave/Woodhouse/GitHub/../../../../res/cacert/cacert.pem';
        $this->_expectedClient = new Curl;
        $this->_expectedClient->setOption(
            CURLOPT_CAINFO,
            $expectedCACertPath
        );
    }

    public function testCreate()
    {
        $actual = $this->_factory->create();
        $expectedBrowser = new Browser($this->_expectedClient);
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateWithCredentials()
    {
        $actual = $this->_factory->create('foo', 'bar');
        $expectedBrowser = new Browser($this->_expectedClient);
        $expectedBrowser->addListener(
            new BasicAuthListener('foo', 'bar')
        );
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
    }
}
