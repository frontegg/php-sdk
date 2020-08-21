<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRequestMethodResolver implements FilterInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if ($request->getMethod() !== 'OPTIONS') {
            return $next($request, $response);
        }

        return new Response(204);
    }
}
