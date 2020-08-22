<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function GuzzleHttp\Psr7\stream_for;

class FronteggRequestHeaderResolver implements FilterInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (
            !$this->getBodyContents($request)
            || in_array('application/json', $request->getHeader('Content-Type'))
        ) {
            return $next($request, $response);
        }

        // In case if content-type is application/x-www-form-urlencoded
        // we need to change to application/json
        if (in_array('application/x-www-form-urlencoded', $request->getHeader('Content-Type'))) {
            $request = $this->setJsonDataToRequestBody($request);
        }
        $request = $request->withHeader('Content-Type', 'application/json');

        return $next($request, $response);
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    protected function setJsonDataToRequestBody(RequestInterface $request): RequestInterface
    {
        $body = $this->getBodyContents($request);
        parse_str($body, $data);
        $stream = stream_for(json_encode($data));

        return $request->withBody($stream);
    }

    /**
     * Returns body contents.
     * Rewinds stream pointer back.
     *
     * @param RequestInterface $request
     *
     * @return string
     */
    protected function getBodyContents(RequestInterface $request): string
    {
        $body = $request->getBody()->getContents();
        $request->getBody()->rewind();

        return $body;
    }
}
