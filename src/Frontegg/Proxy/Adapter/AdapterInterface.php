<?php

namespace Frontegg\Proxy\Adapter;

use Frontegg\Http\ApiRawResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface AdapterInterface
{
    /**
     * Send the request and return the response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface|ApiRawResponse
     */
    public function send(RequestInterface $request);
}