<?php
namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Listener\BasicAuthListener;
use Icecave\Isolator\Isolator;
use Icecave\Woodhouse\TypeCheck\TypeCheck;

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
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $caCertificatePath) {
            $caCertificatePath = __DIR__ . '/../../../../res/cacert/cacert.pem';
        }

        $this->caCertificatePath = $caCertificatePath;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return string
     */
    public function caCertificatePath()
    {
        $this->typeCheck->caCertificatePath(func_get_args());

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
        $this->typeCheck->create(func_get_args());

        $client = new Curl;
        $client->setOption(CURLOPT_CAINFO, $this->caCertificateRealPath());
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
        $this->typeCheck->caCertificateRealPath(func_get_args());

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

    private $caCertificatePath;
    private $caCertificateRealPath;
    private $typeCheck;
}
