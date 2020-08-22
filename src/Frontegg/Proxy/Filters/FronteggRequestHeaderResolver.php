<?php

namespace Frontegg\Proxy\Filters;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggRequestHeaderResolver implements FilterInterface
{
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (
            !$request->getBody()->getContents()
            || in_array('application/json', $request->getHeader('Content-Type'))
        ) {
            return $next($request, $response);
        }

        // In case if content-type is application/x-www-form-urlencoded
        // we need to change to application/json
        $request = $request->withHeader('Content-Type', 'application/json');

        if (in_array('application/x-www-form-urlencoded', $request->getHeader('Content-Type'))) {
            $request = $this->setJsonDataToRequestBody($request);
        }

        return $next($request, $response);
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    protected function setJsonDataToRequestBody(RequestInterface $request): RequestInterface
    {
        $data = urldecode($request->getBody()->getContents());
        $resource = fopen(
            'data://text/plain;base64,' . base64_encode(
                json_encode($data)
            ),
            'r'
        );

        return $request->withBody(new Stream($resource));
    }
}
