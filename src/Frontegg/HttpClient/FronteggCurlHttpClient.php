<?php

namespace Frontegg\HttpClient;

use Frontegg\Exception\FronteggSDKException;
use Frontegg\Http\ApiRawResponse;

/**
 * Class FronteggCurlHttpClient
 *
 * @package Frontegg
 */
class FronteggCurlHttpClient implements FronteggHttpClientInterface
{
    /**
     * @var string The client error message
     */
    protected $curlErrorMessage = '';

    /**
     * @var int The curl client error code
     */
    protected $curlErrorCode = 0;

    /**
     * The raw response from the server
     *
     * @var string|boolean
     */
    protected $rawResponse;

    /**
     * Procedural curl as object
     *
     * @var FronteggCurl
     */
    protected $fronteggCurl;

    /**
     * @param FronteggCurl|null $fronteggCurl
     */
    public function __construct(FronteggCurl $fronteggCurl = null)
    {
        $this->fronteggCurl = $fronteggCurl ?: new FronteggCurl();
    }

    /**
     * @TODO: Refactor this to use Request and Response interfaces from the PSR.
     *
     * @inheritdoc
     */
    public function send(
        string $url,
        string $method,
        string $body,
        array $headers,
        int $timeOut = self::DEFAULT_TIMEOUT
    ): ApiRawResponse {
        $this->openConnection($url, $method, $body, $headers, $timeOut);
        $this->sendRequest();

        if ($curlErrorCode = $this->fronteggCurl->errno()) {
            throw new FronteggSDKException(
                $this->fronteggCurl->error(),
                $curlErrorCode
            );
        }

        // Separate the raw headers from the raw body
        list($rawHeaders, $rawBody) = $this->extractResponseHeadersAndBody();

        $this->closeConnection();

        return new ApiRawResponse($rawHeaders, $rawBody);
    }

    /**
     * Opens a new curl connection.
     *
     * @param string $url     The endpoint to send the request to.
     * @param string $method  The request method.
     * @param string $body    The body of the request.
     * @param array  $headers The request headers.
     * @param int    $timeOut The timeout in seconds for the request.
     */
    public function openConnection(
        string $url,
        string $method,
        string $body,
        array $headers,
        int $timeOut = 60
    ) {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $timeOut,
            CURLOPT_RETURNTRANSFER => true, // Return response as string
            CURLOPT_HEADER => true, // Enable header processing
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        if ($method !== "GET") {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        $this->fronteggCurl->init();
        $this->fronteggCurl->setoptArray($options);
    }

    /**
     * Closes an existing curl connection
     */
    public function closeConnection()
    {
        $this->fronteggCurl->close();
    }

    /**
     * Send the request and get the raw response from curl
     */
    public function sendRequest()
    {
        $this->rawResponse = $this->fronteggCurl->exec();
    }

    /**
     * Compiles the request headers into a curl-friendly format.
     *
     * @param array $headers The request headers.
     *
     * @return array
     */
    public function compileRequestHeaders(array $headers)
    {
        $return = [];

        foreach ($headers as $key => $value) {
            $value = is_array($value) ? ($value[0] ?? '') : $value;

            $return[] = $key . ': ' . $value;
        }

        return $return;
    }

    /**
     * Extracts the headers and the body into a two-part array
     *
     * @return array
     */
    public function extractResponseHeadersAndBody()
    {
        $parts = explode("\r\n\r\n", $this->rawResponse);
        $rawBody = array_pop($parts);
        $rawHeaders = implode("\r\n\r\n", $parts);

        return [trim($rawHeaders), trim($rawBody)];
    }
}
