<?php
namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\BasicAuthListener;
use Icecave\Woodhouse\TypeCheck\TypeCheck;

class GitHubClientFactory
{
    /**
     * @param string|null $username
     * @param string|null $password
     *
     * @return GitHubClient
     */
    public function create($username = null, $password = null)
    {
        TypeCheck::get(__CLASS__)->create(func_get_args());

        $client = new Curl;
        $client->setOption(
            CURLOPT_CAINFO,
            __DIR__ . '/../../../../res/cacert/cacert.pem'
        );
        $browser = new Browser($client);
        if (null !== $username) {
            $browser->addListener(
                new BasicAuthListener($username, $password)
            );
        }

        return new GitHubClient(null, $browser);
    }
}
