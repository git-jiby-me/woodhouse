<?php
namespace Icecave\Woodhouse\GitHub;

use Buzz\Browser;
use Icecave\Woodhouse\TypeCheck\TypeCheck;
use RuntimeException;
use stdClass;

class GitHubClient
{
    /**
     * @param string|null  $url
     * @param Browser|null $browser
     */
    public function __construct(
        $url = null,
        Browser $browser = null
    ) {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $url) {
            $url = 'https://api.github.com/';
        }
        if (null === $browser) {
            $browser = new Browser;
        }

        $this->url = $url;
        $this->browser = $browser;
    }

    /**
     * @return string
     */
    public function url()
    {
        $this->typeCheck->url(func_get_args());

        return $this->url;
    }

    /**
     * @return Browser
     */
    public function browser()
    {
        $this->typeCheck->browser(func_get_args());

        return $this->browser;
    }

    /**
     * @return array<stdClass>
     */
    public function authorizations()
    {
        $this->typeCheck->authorizations(func_get_args());

        return $this->get('authorizations');
    }

    /**
     * @param string|null $namePattern
     * @param string|null $urlPattern
     *
     * @return array<stdClass>
     */
    public function authorizationsMatching($namePattern = null, $urlPattern = null)
    {
        $this->typeCheck->authorizationsMatching(func_get_args());

        if (null === $namePattern) {
            $namePattern = '/.*/';
        }
        if (null === $urlPattern) {
            $urlPattern = '/.*/';
        }

        $authorizations = array();
        foreach ($this->authorizations() as $authorization) {
            if (null === $authorization->note) {
                $name = $authorization->app->name;
            } else {
                $name = $authorization->note;
            }
            if (null === $authorization->note_url) {
                $url = $authorization->app->url;
            } else {
                $url = $authorization->note_url;
            }

            if (preg_match($namePattern, $name) && preg_match($urlPattern, $url)) {
                $authorizations[] = $authorization;
            }
        }

        return $authorizations;
    }

    /**
     * @param array<string> $scopes
     * @param string|null   $note
     * @param string|null   $noteUrl
     *
     * @return stdClass
     */
    public function createAuthorization(
        array $scopes = array(),
        $note = null,
        $noteUrl = null
    ) {
        $this->typeCheck->createAuthorization(func_get_args());

        $data = new stdClass;
        if (array() !== $scopes) {
            $data->scopes = $scopes;
        }
        if (null !== $note) {
            $data->note = $note;
        }
        if (null !== $noteUrl) {
            $data->note_url = $noteUrl;
        }

        return $this->post('authorizations', $data);
    }

    /**
     * @param integer $id
     */
    public function deleteAuthorization($id)
    {
        $this->typeCheck->deleteAuthorization(func_get_args());

        $this->delete(sprintf(
            'authorizations/%s',
            rawurlencode($id)
        ));
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    protected function get($url)
    {
        $this->typeCheck->get(func_get_args());

        $url = $this->url() . $url;
        $response = $this->browser()->get($url);
        if (!$response->isSuccessful()) {
            throw new RuntimeException(sprintf(
                "Unable to get '%s'. Server returned '%s'.",
                $url,
                $response->getReasonPhrase()
            ));
        }

        return $this->parseJson($response->getContent());
    }

    /**
     * @param string $url
     * @param mixed  $data
     *
     * @return mixed
     */
    protected function post($url, $data)
    {
        $this->typeCheck->post(func_get_args());

        $url = $this->url() . $url;
        $response = $this->browser()->post($url, array(), json_encode($data));
        if (!$response->isSuccessful()) {
            throw new RuntimeException(sprintf(
                "Unable to post to '%s'. Server returned '%s'.",
                $url,
                $response->getReasonPhrase()
            ));
        }

        return $this->parseJson($response->getContent());
    }

    /**
     * @param string $url
     */
    protected function delete($url)
    {
        $this->typeCheck->delete(func_get_args());

        $url = $this->url() . $url;
        $response = $this->browser()->delete($url);
        if (!$response->isSuccessful()) {
            throw new RuntimeException(sprintf(
                "Unable to delete '%s'. Server returned '%s'.",
                $url,
                $response->getReasonPhrase()
            ));
        }
    }

    /**
     * @param string $json
     *
     * @return mixed
     */
    protected function parseJson($json)
    {
        $this->typeCheck->parseJson(func_get_args());

        $data = json_decode($json);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Unable to decode response from server.');
        }

        return $data;
    }

    private $url;
    private $browser;
    private $typeCheck;
}
