<?php

namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\BasicAuthListener;
use Icecave\Isolator\Isolator;

class GitHubClientFactory
{
    /**
     * @param string|null   $caCertificatePath
     * @param Isolator|null $isolator
     */
    public function __construct(
        $caCertificatePath = null,
        Isolator $isolator = null
    ) {
        if (null === $caCertificatePath) {
            $caCertificatePath = __DIR__ . '/../../res/cacert/cacert.pem';
        }

        $this->userAgent = null;
        $this->caCertificatePath = $caCertificatePath;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return string|null
     */
    public function userAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function caCertificatePath()
    {
        return $this->caCertificatePath;
    }

    /**
     * @param string|null $username
     * @param string|null $password
     *
     * @return GitHubClient
     */
    public function create($username = null, $password = null)
    {
        $client = new Curl();
        $client->setOption(CURLOPT_CAINFO, $this->caCertificateRealPath());

        if ($userAgent = $this->userAgent()) {
            $client->setOption(CURLOPT_USERAGENT, $userAgent);
        }

        $browser = new Browser($client);

        if (null !== $username) {
            $browser->addListener(
                new BasicAuthListener($username, $password)
            );
        }

        return new GitHubClient(null, $browser);
    }

    /**
     * @return string
     */
    protected function caCertificateRealPath()
    {
        if (null === $this->caCertificateRealPath) {
            $this->caCertificateRealPath = sprintf(
                '%s/cacert-%s.pem',
                $this->isolator->sys_get_temp_dir(),
                $this->isolator->uniqid()
            );
            $this->isolator->copy(
                $this->caCertificatePath(),
                $this->caCertificateRealPath
            );
        }

        return $this->caCertificateRealPath;
    }

    private $userAgent;
    private $caCertificatePath;
    private $caCertificateRealPath;
}
