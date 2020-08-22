<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Proxy\Adapter\AdapterInterface;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Frontegg\Http\ResponseInterface as FronteggResponseInterface;

class FronteggSendRequestResolver implements FilterInterface
{
    /**
     * Maximum count of resending the original request on response errors.
     */
    protected const MAX_RETRY_COUNT = 3;

    /**
     * @var AdapterInterface
     */
    protected $httpClientAdapter;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * FronteggSendRequestResolver constructor.
     *
     * @param AdapterInterface $httpClientAdapter
     * @param Authenticator    $authenticator
     */
    public function __construct(
        AdapterInterface $httpClientAdapter,
        Authenticator $authenticator
    ) {
        $this->httpClientAdapter = $httpClientAdapter;
        $this->authenticator = $authenticator;
    }

    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $retryCount = 0;
        while ($retryCount <= static::MAX_RETRY_COUNT) {
            $response = $this->sendRequest($request);

            if (in_array(
                $response->getStatusCode(),
                $this->getSuccessHttpStatuses()
            )) {
                return $next($request, $response);
            }

            if ($response->getStatusCode() === FronteggResponseInterface::HTTP_STATUS_UNAUTHORIZED) {
                $this->authenticator->validateAuthentication();
            }

            $retryCount++;
        }

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