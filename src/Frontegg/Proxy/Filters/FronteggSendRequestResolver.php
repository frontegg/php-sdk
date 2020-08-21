<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Proxy\Adapter\AdapterInterface;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Frontegg\Http\ResponseInterface as FronteggResponseInterface;

class FronteggSendRequestResolver implements FilterInterface
{
    protected const MAX_RETRY_COUNT = 3;

    /**
     * @var AdapterInterface
     */
    protected $httpClientAdapter;

    /**
     * FronteggResponseAuthCheckResolver constructor.
     *
     * @param AdapterInterface $httpClientAdapter
     */
    public function __construct(AdapterInterface $httpClientAdapter)
    {
        $this->httpClientAdapter = $httpClientAdapter;
    }

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $retryCount = 0;
        while ($retryCount <= static::MAX_RETRY_COUNT) {
//        var_dump($request->getUri(), $request->getMethod());
//        var_dump($request->getHeaders());
            $response = $this->sendRequest($request);
//        var_dump($response->getStatusCode());
//        var_dump($response->getBody()->getContents());
//        exit;

            if (in_array(
                $response->getStatusCode(),
                $this->getSuccessHttpStatuses()
            )
            ) {
                return $next($request, $response);
            }

            $retryCount++;
        }

        $response = $response->withStatus(
            FronteggResponseInterface::HTTP_STATUS_INTERNAL_SERVER_ERROR
        );
        // @TODO: Set body to "Frontegg request failed".
//        $response = $response->withBody(new Stream());

        return $next($request, $response);
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    protected function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->httpClientAdapter->send($request);
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