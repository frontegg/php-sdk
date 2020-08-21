<?php

namespace Frontegg\Proxy\Filters;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggResponseHeaderResolver implements FilterInterface
{
    /**
     * Disable or enable CORS headers.
     *
     * @var bool
     */
    protected $disableCors;

    /**
     * FronteggResponseHeaderResolver constructor.
     *
     * @param bool|null $disableCors
     */
    public function __construct(?bool $disableCors = true)
    {
        $this->disableCors = (bool)$disableCors;
    }

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        if ($this->disableCors) {
            $response = $response->withoutHeader(
                'access-control-allow-methods'
            );
            $response = $response->withoutHeader(
                'access-control-allow-headers'
            );
            $response = $response->withoutHeader('access-control-allow-origin');
            $response = $response->withoutHeader(
                'access-control-allow-credentials'
            );

            return $response;
        }

        return $this->enableCors($request, $response);
    }

    /**
     * Sets CORS headers if they were set in request.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function enableCors(
        RequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        if ($request->hasHeader('access-control-request-method')) {
            $response = $response->withHeader(
                'access-control-allow-methods',
                $request->getHeader('access-control-request-method')
            );
        }

        if ($request->hasHeader('access-control-request-headers')) {
            $response = $response->withHeader(
                'access-control-allow-headers',
                $request->getHeader('access-control-request-headers')
            );
        }

        if ($request->hasHeader('Origin')) {
            $response = $response->withHeader(
                'access-control-allow-origin',
                $request->getHeader('access-control-allow-methods')
            );
            $response = $response->withHeader(
                'access-control-allow-credentials',
                true
            );
        }

        return $response;
    }
}