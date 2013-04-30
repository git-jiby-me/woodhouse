<?php
namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\BasicAuthListener;
use Phake;
use PHPUnit_Framework_TestCase;

class GitHubClientFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('Requires cURL extension.');
        }

        $this->_isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->_factory = new GitHubClientFactory(
            'baz',
            $this->_isolator
        );

        Phake::when($this->_isolator)
            ->sys_get_temp_dir(Phake::anyParameters())
            ->thenReturn('qux')
        ;
        Phake::when($this->_isolator)
            ->uniqid(Phake::anyParameters())
            ->thenReturn('doom')
        ;

        $this->_expectedClient = new Curl;
        $this->_expectedClient->setOption(CURLOPT_CAINFO, 'qux/cacert-doom.pem');
    }

    public function testConstructor()
    {
        $this->assertSame('baz', $this->_factory->caCertificatePath());
    }

    public function testSetUserAgent()
    {
        $this->_factory->setUserAgent('test-agent');
        $this->assertSame('test-agent', $this->_factory->userAgent());
    }

    public function testConstructorDefaults()
    {
        $this->_factory = new GitHubClientFactory;
        $expectedCaCertificatePath = __DIR__;
        for ($i = 0; $i < 5; $i ++) {
            $expectedCaCertificatePath = dirname($expectedCaCertificatePath);
        }
        $expectedCaCertificatePath .= '/lib/Icecave/Woodhouse/GitHub/../../../../res/cacert/cacert.pem';

        $this->assertSame($expectedCaCertificatePath, $this->_factory->caCertificatePath());
    }

    public function testCreate()
    {
        $actual = $this->_factory->create();
        $expectedBrowser = new Browser($this->_expectedClient);
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
        Phake::verify($this->_isolator)->copy('baz', 'qux/cacert-doom.pem');
    }

    public function testCreateWithUserAgent()
    {
        $this->_factory->setUserAgent('test-agent');
        $this->_expectedClient->setOption(CURLOPT_USERAGENT, 'test-agent');
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
