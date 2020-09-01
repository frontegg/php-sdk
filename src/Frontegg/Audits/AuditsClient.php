<?php

namespace Frontegg\Audits;

use Exception;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\Exception\InvalidParameterException;
use Frontegg\Exception\InvalidUrlConfigException;
use Frontegg\Http\RequestInterface;
use Frontegg\Http\Response;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Json\ApiJsonTrait;

class AuditsClient
{
    use ApiJsonTrait;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * AuditsClient constructor.
     *
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @return Authenticator
     */
    public function getAuthenticator(): Authenticator
    {
        return $this->authenticator;
    }

    /**
     * Gets audits data collection by free text filter limited by results count
     * and started from the specified offset for the current tenant ID.
     * Results can be filtered by custom query parameters.
     * Result audits data can be sorted by specified field and sort direction.
     *
     * @param string      $tenantId
     * @param string      $filter        Free text filter
     * @param int         $offset        Zero based index for start the search from
     * @param int|null    $count         Number of results to retrieve
     * @param string|null $sortBy        Field to sort by
     * @param string      $sortDirection Sort direction
     * @param mixed       $filters       Dynamic query params based on the metadata
     *
     * @throws AuthenticationException
     * @throws FronteggSDKException
     * @throws InvalidUrlConfigException
     *
     * @return array
     */
    public function getAudits(
        string $tenantId,
        string $filter = '',
        int $offset = 0,
        ?int $count = null,
        ?string $sortBy = null,
        string $sortDirection = 'ASC',
        ...$filters
    ): array {
        $this->validateAuthentication();

        $accessTokenValue = $this->getAuthenticator()
            ->getAccessToken()
            ->getValue();
        $url = $this->getUrlWithQueryParams(
            $filter,
            $offset,
            $count,
            $sortBy,
            $sortDirection,
            $filters
        );

        $headers = [
            'Content-Type' => 'application/json',
            'x-access-token' => $accessTokenValue,
            'frontegg-tenant-id' => $tenantId,
        ];

        $lastResponse = $this->getHttpClient()->send(
            $url,
            RequestInterface::METHOD_GET,
            '',
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (
            Response::HTTP_STATUS_OK
            !== $lastResponse->getHttpResponseCode()
        ) {
            throw new AuthenticationException($lastResponse->getBody());
        }

        $auditLogs = $this->getDecodedJsonData($lastResponse->getBody());

        if (null === $auditLogs) {
            throw new Exception('Something strange happened');
        }

        return $auditLogs;
    }

    /**
     * Sends audit log data into the Frontegg system.
     * Returns created audit log data.
     *
     * @param string $tenantId
     * @param array  $auditLog   Audits parameters:
     *                           user: string - User email
     *                           resource: string - Source of log event
     *                           action: string - Log event name
     *                           severity: string (required) - Log level
     *                           ip: string - User IP
     *
     * @throws AuthenticationException
     * @throws FronteggSDKException
     * @throws InvalidParameterException
     * @throws InvalidUrlConfigException
     *
     * @return array
     */
    public function sendAudit($tenantId, $auditLog): array
    {
        if (!isset($auditLog['severity'])) {
            throw new InvalidParameterException(
                'Invalid parameters. Severity is required'
            );
        }

        $this->validateAuthentication();

        $accessTokenValue = $this->getAuthenticator()
            ->getAccessToken()
            ->getValue();
        $headers = [
            'Content-Type' => 'application/json',
            'x-access-token' => $accessTokenValue,
            'frontegg-tenant-id' => $tenantId,
        ];

        $lastResponse = $this->getHttpClient()->send(
            $this->getAuditsServiceUrl(),
            RequestInterface::METHOD_POST,
            json_encode($auditLog),
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (
            !in_array(
                $lastResponse->getHttpResponseCode(),
                Response::getSuccessHttpStatuses()
            )
        ) {
            throw new AuthenticationException($lastResponse->getBody());
        }

        $auditLogData = $this->getDecodedJsonData($lastResponse->getBody());

        if (null === $auditLogData) {
            throw new FronteggSDKException(
                'An error occurred while response data was decoding'
            );
        }

        return $auditLogData;
    }

    /**
     * Returns combined service URL with query string parameters.
     *
     * @param string      $filter
     * @param int         $offset
     * @param int|null    $count
     * @param string|null $sortBy
     * @param string      $sortDirection
     * @param array       $filters
     *
     * @throws InvalidUrlConfigException
     *
     * @return string
     */
    protected function getUrlWithQueryParams(
        string $filter,
        int $offset,
        ?int $count,
        ?string $sortBy,
        string $sortDirection,
        array $filters
    ): string {
        return sprintf(
            '%s?%s',
            $this->getAuditsServiceUrl(),
            http_build_query(
                array_merge(
                    [
                        'filter' => $filter,
                        'offset' => $offset,
                        'count' => $count,
                        'sortBy' => $sortBy,
                        'sortDirection' => $sortDirection,
                    ],
                    $filters
                )
            )
        );
    }

    /**
     * Returns Audits service URL from config.
     *
     * @throws InvalidUrlConfigException
     *
     * @return string
     */
    protected function getAuditsServiceUrl(): string
    {
        return $this->authenticator->getConfig()
            ->getServiceUrl(
                Config::AUDITS_SERVICE
            );
    }

    /**
     * Returns HTTP client.
     *
     * @return FronteggHttpClientInterface
     */
    protected function getHttpClient(): FronteggHttpClientInterface
    {
        return $this->authenticator->getClient();
    }

    /**
     * Validates access token.
     * Throws an exception on failure.
     *
     * @throws AuthenticationException
     *
     * @return void
     */
    protected function validateAuthentication(): void
    {
        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }
    }
}
