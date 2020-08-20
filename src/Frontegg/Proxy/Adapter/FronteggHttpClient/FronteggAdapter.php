<?php

namespace Frontegg\Proxy\Adapter\FronteggHttpClient;

use Frontegg\HttpClient\FronteggCurlHttpClient;
use Frontegg\Proxy\Adapter\AdapterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggAdapter implements AdapterInterface
{
    /**
     * The FronteggCurlHttpClient instance
     * @var FronteggCurlHttpClient
     */
    protected $client;

    /**
     * @param FronteggCurlHttpClient $client
     */
    public function __construct(FronteggCurlHttpClient $client)
    {
        $this->client = $client;
    }


    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request->getUri(), $request->getMethod(), $request->getBody(), $request->getHeaders());
    }
}