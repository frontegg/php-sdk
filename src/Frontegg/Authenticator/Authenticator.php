<?php

namespace Frontegg\Authenticator;

use DateTime;
use Frontegg\Config\Config;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use JsonException;

class Authenticator
{
    /**
     * Frontegg configuration.
     *
     * @var Config
     */
    protected $fronteggConfig;

    /**
     * @var FronteggHttpClientInterface
     */
    protected $client;

    /**
     * @var AccessToken|null
     */
    protected $accessToken;

    /**
     * @var ApiRawResponse|null
     */
    protected $lastResponse;

    /**
     * @var array|null
     * @todo: Refactor this into separate class.
     */
    protected $error;

    /**
     * Authenticator constructor.
     *
     * @param Config $fronteggConfig
     * @param FronteggHttpClientInterface $client
     */
    public function __construct(
        Config $fronteggConfig,
        FronteggHttpClientInterface $client
    ) {
        $this->fronteggConfig = $fronteggConfig;
        $this->client = $client;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->fronteggConfig;
    }

    /**
     * @return AccessToken|null
     */
    public function getAccessToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @return ApiRawResponse|null
     */
    public function getLastResponse(): ?ApiRawResponse
    {
        return $this->lastResponse;
    }

    /**
     * @return array|null
     */
    public function getError(): ?array
    {
        return $this->error;
    }

    /**
     * Authenticate client using client ID and secret key. Retrieves an access
     * token.
     *
     * @return void
     */
    public function authenticate(): void
    {
        $url = $this->fronteggConfig->getServiceUrl(Config::SERVICE_AUTHENTICATION);
        $body = json_encode([
            'clientId' => $this->fronteggConfig->getClientId(),
            'clientSecret' => $this->fronteggConfig->getClientSecret(),
        ]);

        $this->lastResponse = $this->client->send(
            $url,
            'POST',
            $body,
            [
                'Content-type: application/json',
            ],
            10
        );

        if (200 !== $this->lastResponse->getHttpResponseCode()) {
            $this->setErrorFromResponseData();

            return;
        }

        $this->setAccessTokenFromResponseData();
    }

    /**
     * Validate the current access token. Get a new access token if the current
     * was expired.
     *
     * @return void
     */
    public function validateAuthentication(): void
    {
        if ($this->accessToken && $this->accessToken->isValid()) {
            return;
        }

        $this->authenticate();
    }

    /**
     * Sets access token from the last response data.
     * Sets error to null.
     *
     * @todo Refactor this.
     *
     * @return void
     */
    protected function setAccessTokenFromResponseData(): void
    {
        if (empty($this->lastResponse->getBody())) {
            $this->error = [
                'error' => 'Invalid JSON',
                'message' => 'An empty string can\'t be parsed as valid JSON.',
            ];
            $this->accessToken = null;

            return;
        }

        try {
            $responseBodyDecoded = json_decode($this->lastResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->error = [
                'error' => 'Invalid JSON',
                'message' => $e->getMessage(),
            ];
            $this->accessToken = null;

            return;
        }

        $expiresAt = new DateTime(sprintf('+%d seconds', $responseBodyDecoded['expiresIn']));
        $this->accessToken = new AccessToken($responseBodyDecoded['token'], $expiresAt);
        $this->error = null;
    }

    /**
     * Sets an error data from response data.
     * Sets access token to null.
     *
     * @todo Refactor this.
     *
     * @return void
     */
    protected function setErrorFromResponseData(): void
    {
        if (empty($this->lastResponse->getBody())) {
            $this->error = [
                'error' => 'Invalid JSON',
                'message' => 'An empty string can\'t be parsed as valid JSON.',
            ];
            $this->accessToken = null;

            return;
        }

        try {
            $this->error = json_decode($this->lastResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->error = [
                'error' => 'Invalid JSON',
                'message' => $e->getMessage(),
            ];
            $this->accessToken = null;

            return;
        }

        $this->accessToken = null;
    }
}