<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Http\ResponseInterface as FronteggResponseInterface;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FronteggResponseErrorResolver implements FilterInterface
{
    const FRONTEGG_REQUEST_FAILED = 'Frontegg request failed';

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        if (in_array(
            $response->getStatusCode(),
            $this->getSuccessHttpStatuses()
        )) {
            return $response;
        }

        $response = $response->withStatus(
            FronteggResponseInterface::HTTP_STATUS_INTERNAL_SERVER_ERROR
        );

        $response = $this->setServerErrorToResponse($response);

        return $response;
    }

    /**
     * @param $response
     *
     * @return ResponseInterface
     */
    protected function setServerErrorToResponse($response): ResponseInterface
    {
        $resource = fopen(
            'data://text/plain;base64,'.base64_encode(
                self::FRONTEGG_REQUEST_FAILED
            ),
            'r'
        );

        return $response->withBody(new Stream($resource));
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
