<?php
namespace Icecave\Woodhouse\GitHub;

use Phake;
use PHPUnit_Framework_TestCase;
use stdClass;

class GitHubClientTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_browser = Phake::mock('Buzz\Browser');
        $this->_client = Phake::partialMock(
            __NAMESPACE__ . '\GitHubClient',
            'foo/',
            $this->_browser
        );
    }

    protected function responseFixture($content, $isSuccessful = true)
    {
        $response = Phake::mock('Buzz\Message\Response');
        Phake::when($response)
            ->isSuccessful(Phake::anyParameters())
            ->thenReturn($isSuccessful)
        ;
        if ($isSuccessful) {
            Phake::when($response)
                ->getContent(Phake::anyParameters())
                ->thenReturn($content)
            ;
        } else {
            Phake::when($response)
                ->getReasonPhrase(Phake::anyParameters())
                ->thenReturn($content)
            ;
        }

        return $response;
    }

    public function testConstruct()
    {
        $this->assertSame('foo/', $this->_client->url());
        $this->assertSame($this->_browser, $this->_client->browser());
    }

    public function testConstructDefaults()
    {
        $this->_client = new GitHubClient;

        $this->assertInstanceOf('Buzz\Browser', $this->_client->browser());
    }

    public function testAuthorizations()
    {
        $json = <<<'EOD'
[
  {
    "token": "bar",
    "note": "baz",
    "note_url": "qux"
  }
]
EOD;
        $expected0 = new stdClass;
        $expected0->token = 'bar';
        $expected0->note = 'baz';
        $expected0->note_url = 'qux';
        $expected = array($expected0);
        Phake::when($this->_browser)
            ->get(Phake::anyParameters())
            ->thenReturn($this->responseFixture($json))
        ;
        $actual = $this->_client->authorizations();

        $this->assertEquals($expected, $actual);
        Phake::verify($this->_browser)
            ->get('foo/authorizations')
        ;
    }

    public function authorizationsMatchingData()
    {
        $authorization0 = new stdClass;
        $authorization0->app = new stdClass;
        $authorization0->app->name = 'foo';
        $authorization0->app->url = 'bar';
        $authorization0->note = null;
        $authorization0->note_url = null;
        $authorization1 = new stdClass;
        $authorization1->app = new stdClass;
        $authorization1->app->name = 'bar';
        $authorization1->app->url = 'baz';
        $authorization1->note = null;
        $authorization1->note_url = null;
        $authorization2 = new stdClass;
        $authorization2->app = new stdClass;
        $authorization2->app->name = 'foobar';
        $authorization2->app->url = 'barbaz';
        $authorization2->note = null;
        $authorization2->note_url = null;
        $authorization3 = new stdClass;
        $authorization3->app = new stdClass;
        $authorization3->app->name = 'foo (API)';
        $authorization3->app->url = 'qux';
        $authorization3->note = 'foo';
        $authorization3->note_url = 'bar';
        $authorization4 = new stdClass;
        $authorization4->app = new stdClass;
        $authorization4->app->name = 'bar (API)';
        $authorization4->app->url = 'doom';
        $authorization4->note = 'bar';
        $authorization4->note_url = 'baz';
        $authorization5 = new stdClass;
        $authorization5->app = new stdClass;
        $authorization5->app->name = 'foobar (API)';
        $authorization5->app->url = 'splat';
        $authorization5->note = 'foobar';
        $authorization5->note_url = 'barbaz';
        $authorizations = array(
            $authorization0,
            $authorization1,
            $authorization2,
            $authorization3,
            $authorization4,
            $authorization5,
        );

        $data = array();

        $data['Match by name'] = array(
            $authorizations,
            '/foo/',
            null,
            array(
                $authorization0,
                $authorization2,
                $authorization3,
                $authorization5,
            )
        );

        $data['Match by url'] = array(
            $authorizations,
            null,
            '/baz/',
            array(
                $authorization1,
                $authorization2,
                $authorization4,
                $authorization5,
            )
        );

        $data['Match by name and url'] = array(
            $authorizations,
            '/foo/',
            '/baz/',
            array(
                $authorization2,
                $authorization5,
            )
        );

        return $data;
    }

    /**
     * @dataProvider authorizationsMatchingData
     */
    public function testAuthorizationsMatching(array $authorizations, $namePattern, $urlPattern, array $expected)
    {
        Phake::when($this->_client)
            ->authorizations(Phake::anyParameters())
            ->thenReturn($authorizations)
        ;
        $actual = $this->_client->authorizationsMatching($namePattern, $urlPattern);

        $this->assertSame($expected, $actual);
        Phake::verify($this->_client)->authorizations();
    }

    public function testAuthorizationsFailureInvalidJson()
    {
        Phake::when($this->_browser)
            ->get(Phake::anyParameters())
            ->thenReturn($this->responseFixture('['))
        ;

        $this->setExpectedException(
            'RuntimeException',
            'Unable to decode response from server.'
        );
        $this->_client->authorizations();
    }

    public function testAuthorizationsFailureHTTP()
    {
        Phake::when($this->_browser)
            ->get(Phake::anyParameters())
            ->thenReturn($this->responseFixture('bar', false))
        ;

        $this->setExpectedException(
            'RuntimeException',
            "Unable to get 'foo/authorizations'. Server returned 'bar'."
        );
        $this->_client->authorizations();
    }

    public function testCreateAuthorization()
    {
        $json = <<<'EOD'
[
  {
    "token": "bar",
    "note": "baz",
    "note_url": "qux"
  }
]
EOD;
        $expected0 = new stdClass;
        $expected0->token = 'bar';
        $expected0->note = 'baz';
        $expected0->note_url = 'qux';
        $expected = array($expected0);
        Phake::when($this->_browser)
            ->post(Phake::anyParameters())
            ->thenReturn($this->responseFixture($json))
        ;
        $actual = $this->_client->createAuthorization();

        $this->assertEquals($expected, $actual);
        Phake::verify($this->_browser)
            ->post(
                'foo/authorizations',
                array(),
                '{}'
            )
        ;
    }

    public function testCreateAuthorizationAllOptions()
    {
        $json = <<<'EOD'
[
  {
    "token": "bar",
    "note": "baz",
    "note_url": "qux"
  }
]
EOD;
        $expected0 = new stdClass;
        $expected0->token = 'bar';
        $expected0->note = 'baz';
        $expected0->note_url = 'qux';
        $expected = array($expected0);
        Phake::when($this->_browser)
            ->post(Phake::anyParameters())
            ->thenReturn($this->responseFixture($json))
        ;
        $actual = $this->_client->createAuthorization(
            array('doom', 'splat'),
            'ping',
            'pong'
        );

        $this->assertEquals($expected, $actual);
        Phake::verify($this->_browser)
            ->post(
                'foo/authorizations',
                array(),
                '{"scopes":["doom","splat"],"note":"ping","note_url":"pong"}'
            )
        ;
    }

    public function testCreateAuthorizationFailureInvalidJson()
    {
        Phake::when($this->_browser)
            ->post(Phake::anyParameters())
            ->thenReturn($this->responseFixture('{'))
        ;

        $this->setExpectedException(
            'RuntimeException',
            'Unable to decode response from server.'
        );
        $this->_client->createAuthorization();
    }

    public function testCreateAuthorizationFailureHTTP()
    {
        Phake::when($this->_browser)
            ->post(Phake::anyParameters())
            ->thenReturn($this->responseFixture('bar', false))
        ;

        $this->setExpectedException(
            'RuntimeException',
            "Unable to post to 'foo/authorizations'. Server returned 'bar'."
        );
        $this->_client->createAuthorization();
    }

    public function testDeleteAuthorization()
    {
        Phake::when($this->_browser)
            ->delete(Phake::anyParameters())
            ->thenReturn($this->responseFixture(''))
        ;
        $this->_client->deleteAuthorization(111);

        Phake::verify($this->_browser)->delete('foo/authorizations/111');
    }

    public function testDeleteAuthorizationFailureHTTP()
    {
        Phake::when($this->_browser)
            ->delete(Phake::anyParameters())
            ->thenReturn($this->responseFixture('bar', false))
        ;

        $this->setExpectedException(
            'RuntimeException',
            "Unable to delete 'foo/authorizations/111'. Server returned 'bar'."
        );
        $this->_client->deleteAuthorization(111);
    }
}
