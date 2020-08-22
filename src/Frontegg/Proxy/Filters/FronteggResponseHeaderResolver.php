<?php

namespace Frontegg\Proxy\Filters;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggResponseHeaderResolver implements FilterInterface
{
    protected const HEADER_ACCESS_CONTROL_ALLOW_METHODS = 'access-control-allow-methods';
    protected const HEADER_ACCESS_CONTROL_ALLOW_HEADERS = 'access-control-allow-headers';
    protected const HEADER_ACCESS_CONTROL_ALLOW_ORIGIN = 'access-control-allow-origin';
    protected const HEADER_ACCESS_CONTROL_ALLOW_CREDENTIALS = 'access-control-allow-credentials';

    protected const HEADER_ACCESS_CONTROL_REQUEST_METHOD = 'access-control-request-method';
    protected const HEADER_ACCESS_CONTROL_REQUEST_HEADERS = 'access-control-request-headers';
    protected const HEADER_ORIGIN = 'Origin';

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
                static::HEADER_ACCESS_CONTROL_ALLOW_METHODS
            );
            $response = $response->withoutHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_HEADERS
            );
            $response = $response->withoutHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_ORIGIN
            );
            $response = $response->withoutHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_CREDENTIALS
            );

            return $response;
        }

        return $this->enableCors($request, $response);
    }

    /**
     * Sets CORS headers if they were set in request.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function enableCors(
        RequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        if ($request->hasHeader(static::HEADER_ACCESS_CONTROL_REQUEST_METHOD)) {
            $response = $response->withHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_METHODS,
                $request->getHeader(static::HEADER_ACCESS_CONTROL_REQUEST_METHOD)
            );
        }

        if ($request->hasHeader(static::HEADER_ACCESS_CONTROL_REQUEST_HEADERS)) {
            $response = $response->withHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_HEADERS,
                $request->getHeader(static::HEADER_ACCESS_CONTROL_REQUEST_HEADERS)
            );
        }

        if ($request->hasHeader(static::HEADER_ORIGIN)) {
            $response = $response->withHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_ORIGIN,
                $request->getHeader(static::HEADER_ORIGIN)
            );
            $response = $response->withHeader(
                static::HEADER_ACCESS_CONTROL_ALLOW_CREDENTIALS,
                'true'
            );
        }

        return $response;
    }
}
