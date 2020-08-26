<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Http\Response as FronteggResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function GuzzleHttp\Psr7\stream_for;

class FronteggResponseErrorResolver implements FilterInterface
{
    protected const FRONTEGG_REQUEST_FAILED = 'Frontegg request failed';

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        if (
            in_array(
                $response->getStatusCode(),
                FronteggResponse::getSuccessHttpStatuses()
            )
        ) {
            return $response;
        }

        $response = $response->withStatus(
            FronteggResponse::HTTP_STATUS_INTERNAL_SERVER_ERROR
        );

        $response = $this->setServerErrorToResponse($response);

        return $response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function setServerErrorToResponse(ResponseInterface $response): ResponseInterface
    {
        $stream = stream_for(self::FRONTEGG_REQUEST_FAILED);

        return $response->withBody($stream);
    }
}
