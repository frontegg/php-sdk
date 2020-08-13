<?php

namespace Frontegg\HttpClient;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Exception;

/**
 * Class HttpClientFactory
 *
 * Creates HTTP client class depending on installed PHP extensions or libraries.
 *
 * @package Frontegg
 */
class HttpClientFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param FronteggHttpClientInterface|Client|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of Frontegg\HttpClient\FronteggHttpClientInterface.
     *
     * @return FronteggHttpClientInterface
     */
    public static function createHttpClient($handler): FronteggHttpClientInterface
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }

        if ($handler instanceof FronteggHttpClientInterface) {
            return $handler;
        }

        if ('stream' === $handler) {
            return new FronteggStreamHttpClient();
        }
        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new FronteggCurlHttpClient();
        }

        if ('guzzle' === $handler && !class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }

        if ($handler instanceof Client) {
            return new FronteggGuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new FronteggGuzzleHttpClient();
        }

        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Frontegg\HttpClient\FronteggHttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return FronteggHttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (extension_loaded('curl')) {
            return new FronteggCurlHttpClient();
        }

        if (class_exists('GuzzleHttp\Client')) {
            return new FronteggGuzzleHttpClient();
        }

        return new FronteggStreamHttpClient();
    }
}
