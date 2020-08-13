<?php

namespace Frontegg\HttpClient;

use Frontegg\Exception\FronteggSDKException;
use Frontegg\Http\ApiRawResponse;

class FronteggStreamHttpClient implements FronteggHttpClientInterface
{
    /**
     * @var FronteggStream Procedural stream wrapper as object.
     */
    protected $fronteggStream;

    /**
     * @param FronteggStream|null Procedural stream wrapper as object.
     */
    public function __construct(FronteggStream $fronteggStream = null)
    {
        $this->fronteggStream = $fronteggStream ?: new FronteggStream();
    }

    /**
     * @inheritdoc
     */
    public function send($url, $method, $body, array $headers, $timeOut): ApiRawResponse
    {
        $options = [
            'http' => [
                'method' => $method,
                'header' => $this->compileHeader($headers),
                'content' => $body,
                'timeout' => $timeOut,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true, // All root certificates are self-signed
            ],
        ];

        $this->fronteggStream->streamContextCreate($options);
        $rawBody = $this->fronteggStream->fileGetContents($url);
        $rawHeaders = $this->fronteggStream->getResponseHeaders();

        if ($rawBody === false || empty($rawHeaders)) {
            throw new FronteggSDKException('Stream returned an empty response', 660);
        }

        $rawHeaders = implode("\r\n", $rawHeaders);

        return new ApiRawResponse($rawHeaders, $rawBody);
    }

    /**
     * Formats the headers for use in the stream wrapper.
     *
     * @param array $headers The request headers.
     *
     * @return string
     */
    public function compileHeader(array $headers)
    {
        $header = [];
        foreach ($headers as $k => $v) {
            $header[] = $k . ': ' . $v;
        }

        return implode("\r\n", $header);
    }
}
