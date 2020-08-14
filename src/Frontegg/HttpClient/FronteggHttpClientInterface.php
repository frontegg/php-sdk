<?php

namespace Frontegg\HttpClient;

use Frontegg\Http\ApiRawResponse;

/**
 * Interface FronteggHttpClientInterface
 *
 * @package Frontegg
 */
interface FronteggHttpClientInterface
{
    /**
     * Sends a request to the server and returns the raw response.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     *
     * @return \Frontegg\Http\ApiRawResponse Raw response from the server.
     *
     * @throws \Frontegg\Exception\FronteggSDKException
     */
    public function send(string $url, string $method, string $body, array $headers, int $timeOut): ApiRawResponse;
}
