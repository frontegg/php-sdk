<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Http\ResponseInterface as FronteggResponseInterface;
use GuzzleHttp\Psr7\Stream;
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
                $this->getSuccessHttpStatuses()
            )
        ) {
            return $response;
        }

        $response = $response->withStatus(
            FronteggResponseInterface::HTTP_STATUS_INTERNAL_SERVER_ERROR
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

    /**
     * @return int[]
     */
    protected function getSuccessHttpStatuses(): array
    {
        return [
            FronteggResponseInterface::HTTP_STATUS_OK,
            FronteggResponseInterface::HTTP_STATUS_CREATED,
            FronteggResponseInterface::HTTP_STATUS_ACCEPTED,
            FronteggResponseInterface::HTTP_STATUS_NON_AUTHORITATIVE_INFORMATION,
            FronteggResponseInterface::HTTP_STATUS_NO_CONTENT,
            FronteggResponseInterface::HTTP_STATUS_RESET_CONTENT,
            FronteggResponseInterface::HTTP_STATUS_PARTIAL_CONTENT,
            FronteggResponseInterface::HTTP_STATUS_MULTI_STATUS,
            FronteggResponseInterface::HTTP_STATUS_ALREADY_REPORTED,
        ];
    }
}
