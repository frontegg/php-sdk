<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRequestHeaderResolver implements FilterInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!$request->getBody() || $request->getHeader('Content-Type') === 'application/json') {
            return $next($request, $response);
        }

        // In case if content-type is application/x-www-form-urlencoded
        // we need to change to application/json
        $request = $request->withHeader('Content-Type', 'application/json');

        // @TODO: Decoding body from url-encoded to JSON.

        return $next($request, $response);
    }
}
