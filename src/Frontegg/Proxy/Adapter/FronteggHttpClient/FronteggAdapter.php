<?php

namespace Frontegg\Proxy\Adapter\FronteggHttpClient;

use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\AdapterInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggAdapter implements AdapterInterface
{
    /**
     * The FronteggCurlHttpClient instance
     *
     * @var FronteggHttpClientInterface
     */
    protected $client;

    /**
     * FronteggAdapter constructor.
     *
     * @param FronteggHttpClientInterface $client
     */
    public function __construct(FronteggHttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param RequestInterface $request
     *
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return ResponseInterface
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $apiRawResponse = $this->client->send(
            $request->getUri(),
            $request->getMethod(),
            $request->getBody(),
            $request->getHeaders()
        );

         return $this->getAdaptedPsrResponse($apiRawResponse);
    }

    /**
     * @param ApiRawResponse $apiRawResponse
     *
     * @return ResponseInterface
     */
    protected function getAdaptedPsrResponse(ApiRawResponse $apiRawResponse): ResponseInterface
    {
        return new Response(
            $apiRawResponse->getHttpResponseCode(),
            $apiRawResponse->getHeaders(),
            $apiRawResponse->getBody()
        );
    }
}
