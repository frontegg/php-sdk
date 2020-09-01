<?php

namespace Frontegg\Authenticator;

use DateTime;
use Frontegg\Config\Config;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Http\RequestInterface;
use Frontegg\Http\Response;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Json\ApiJsonTrait;

class Authenticator
{
    use ApiJsonTrait;

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
     * Authenticator constructor.
     *
     * @param Config                      $fronteggConfig
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
     * @return FronteggHttpClientInterface
     */
    public function getClient(): FronteggHttpClientInterface
    {
        return $this->client;
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
     * Authenticate client using client ID and secret key. Retrieves an access
     * token.
     *
     * @return void
     */
    public function authenticate(): void
    {
        $url = $this->fronteggConfig->getServiceUrl(
            Config::AUTHENTICATION_SERVICE
        );
        $body = json_encode(
            [
                'clientId' => $this->fronteggConfig->getClientId(),
                'secret' => $this->fronteggConfig->getClientSecret(),
            ]
        );

        $this->lastResponse = $this->client->send(
            $url,
            RequestInterface::METHOD_POST,
            $body,
            ['Content-Type' => 'application/json'],
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (
            Response::HTTP_STATUS_OK
            !== $this->lastResponse->getHttpResponseCode()
        ) {
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
     * @return void
     */
    protected function setAccessTokenFromResponseData(): void
    {
        $responseBodyDecoded = $this->getDecodedJsonData(
            $this->lastResponse->getBody()
        );

        if ($responseBodyDecoded === null) {
            $this->accessToken = null;

            return;
        }

        if (
            !isset($responseBodyDecoded['token'])
            || !isset($responseBodyDecoded['expiresIn'])
        ) {
            $this->setApiError(
                'Bad credentials',
                'Invalid token or expires in value.',
                null
            );
            $this->accessToken = null;

            return;
        }

        $expiresAt = new DateTime(
            sprintf('+%d seconds', $responseBodyDecoded['expiresIn'])
        );
        $this->accessToken = new AccessToken(
            $responseBodyDecoded['token'],
            $expiresAt
        );
        $this->apiError = null;
    }

    /**
     * Sets an error data from response data.
     * Sets access token to null.
     *
     * @return void
     */
    protected function setErrorFromResponseData(): void
    {
        $errorDecoded = $this->getDecodedJsonData(
            $this->lastResponse->getBody()
        );

        if ($errorDecoded === null) {
            $this->accessToken = null;

            return;
        }

        $this->setApiError(
            $errorDecoded['error'] ?? '',
            $errorDecoded['message'] ? print_r($errorDecoded['message'], true) : '',
            $errorDecoded['statusCode'] ?? null
        );
        $this->accessToken = null;
    }
}
