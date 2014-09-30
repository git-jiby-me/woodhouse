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

        $this->isolator = Phake::mock('Icecave\Isolator\Isolator');
        $this->factory = new GitHubClientFactory(
            'baz',
            $this->isolator
        );

        Phake::when($this->isolator)
            ->sys_get_temp_dir(Phake::anyParameters())
            ->thenReturn('qux')
        ;
        Phake::when($this->isolator)
            ->uniqid(Phake::anyParameters())
            ->thenReturn('doom')
        ;

        $this->expectedClient = new Curl();
        $this->expectedClient->setOption(CURLOPT_CAINFO, 'qux/cacert-doom.pem');
    }

    public function testConstructor()
    {
        $this->assertSame('baz', $this->factory->caCertificatePath());
    }

    public function testSetUserAgent()
    {
        $this->factory->setUserAgent('test-agent');
        $this->assertSame('test-agent', $this->factory->userAgent());
    }

    public function testConstructorDefaults()
    {
        $expectedCaCertificatePath = __DIR__ . '/../../../res/cacert/cacert.pem';

        $this->assertTrue(file_exists($expectedCaCertificatePath));

        $this->factory = new GitHubClientFactory();

        $this->assertSame(
            realpath($expectedCaCertificatePath),
            realpath($this->factory->caCertificatePath())
        );
    }

    public function testCreate()
    {
        $actual = $this->factory->create();
        $expectedBrowser = new Browser($this->expectedClient);
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
        Phake::verify($this->isolator)->copy('baz', 'qux/cacert-doom.pem');
    }

    public function testCreateWithUserAgent()
    {
        $this->factory->setUserAgent('test-agent');
        $this->expectedClient->setOption(CURLOPT_USERAGENT, 'test-agent');
        $actual = $this->factory->create();
        $expectedBrowser = new Browser($this->expectedClient);
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateWithCredentials()
    {
        $actual = $this->factory->create('foo', 'bar');
        $expectedBrowser = new Browser($this->expectedClient);
        $expectedBrowser->addListener(
            new BasicAuthListener('foo', 'bar')
        );
        $expected = new GitHubClient(null, $expectedBrowser);

        $this->assertEquals($expected, $actual);
    }
}
