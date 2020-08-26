<?php

namespace Frontegg\Proxy\Filters;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Http\Response as FronteggResponse;
use Frontegg\Proxy\Adapter\AdapterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

            if (
                in_array(
                    $response->getStatusCode(),
                    FronteggResponse::getSuccessHttpStatuses()
                )
            ) {
                return $next($request, $response);
            }

            if ($response->getStatusCode() === FronteggResponse::HTTP_STATUS_UNAUTHORIZED) {
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
}
