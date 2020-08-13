<?php

namespace Frontegg;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\HttpClient\HttpClientFactory;

class Frontegg
{
    /**
     * @const string Version number of the Frontegg PHP SDK.
     */
    const VERSION = '0.1.0';

    /**
     * @const string Default API version for requests.
     */
    const DEFAULT_API_VERSION = 'v1.0';

    /**
     * @const string The name of the environment variable that contains the client ID.
     */
    const CLIENT_ID_ENV_NAME = 'FRONTEGG_CLIENT_ID';

    /**
     * @const string The name of the environment variable that contains the client secret key.
     */
    const CLIENT_SECRET_ENV_NAME = 'FRONTEGG_CLIENT_SECRET_KEY';

    /**
     * @const string Default API version for requests.
     */
    const DEFAULT_API_BASE_URL = 'https://api.frontegg.com';

    /**
     * Frontegg authenticator instance.
     *
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var FronteggHttpClientInterface
     */
    protected $client;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Frontegg constructor.
     *
     * @param array $config
     *
     * @throws FronteggSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'clientId' => getenv(static::CLIENT_ID_ENV_NAME),
            'clientSecret' => getenv(static::CLIENT_SECRET_ENV_NAME),
            'apiBaseUrl' => static::DEFAULT_API_BASE_URL,
            'apiUrls' => [],
            'apiVersion' => static::DEFAULT_API_VERSION,
            'httpClientHandler' => null,
        ], $config);

        if (!$config['clientId']) {
            throw new FronteggSDKException('Required "clientId" key not supplied in config and could not find fallback environment variable "' . static::CLIENT_ID_ENV_NAME . '"');
        }
        if (!$config['clientSecret']) {
            throw new FronteggSDKException('Required "clientSecret" key not supplied in config and could not find fallback environment variable "' . static::CLIENT_SECRET_ENV_NAME . '"');
        }

        $this->config = new Config(
            $config['clientId'],
            $config['clientSecret'],
            $config['apiBaseUrl'],
            $config['apiUrls']
        );
        $this->client = $config['httpClientHandler'] ?? new FronteggCurlHttpClient();

        $this->authenticator = new Authenticator($this->config, $this->client);
        // @TODO: Instantiate Audits, Events, Middleware
    }

    /**
     * @return Authenticator
     */
    public function getAuthenticator(): Authenticator
    {
        return $this->authenticator;
    }

    /**
     * @return FronteggHttpClientInterface
     */
    public function getClient(): FronteggHttpClientInterface
    {
        return $this->client;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Initialize Frontegg service by authenticating into the Frontegg API.
     *
     * @return void
     */
    public function init(): void
    {
        $this->authenticator->authenticate();
    }
}