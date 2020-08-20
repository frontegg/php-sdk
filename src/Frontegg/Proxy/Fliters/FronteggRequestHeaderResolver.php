<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRequestHeaderResolver implements FilterInterface
{

    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!$request->getBody()) {
            return $next($request, $response);
        }

        $request->withHeader('Content-Type', 'application/json');

        return $next($request, $response);
    }
}