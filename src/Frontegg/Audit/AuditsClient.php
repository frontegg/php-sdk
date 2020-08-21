<?php

namespace Frontegg\Audit;

use Exception;
use Frontegg\Authenticator\ApiError;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\Exception\InvalidParameterException;
use Frontegg\Exception\InvalidUrlConfigException;
use Frontegg\Http\RequestInterface;
use Frontegg\Http\ResponseInterface;
use JsonException;

class AuditsClient
{
    protected const JSON_DECODE_DEPTH = 512;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var ApiError|null
     */
    protected $apiError;

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
        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        // @todo: Refactor this.

        $httpClient = $this->authenticator->getClient();
        $accessTokenValue = $this->getAuthenticator()
            ->getAccessToken()
            ->getValue();
        $fronteggConfig = $this->authenticator->getConfig();
        $url = $fronteggConfig->getServiceUrl(
            Config::AUDITS_SERVICE
        );
        $body = json_encode(
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
        );

        $headers = [
            'Content-Type' => 'application/json',
            'x-access-token' => $accessTokenValue,
            'frontegg-tenant-id' => $tenantId,
        ];

        $lastResponse = $httpClient->send(
            $url,
            RequestInterface::METHOD_GET,
            $body,
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (ResponseInterface::HTTP_STATUS_OK !== $lastResponse->getHttpResponseCode()) {
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

        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        // @todo: Refactor this.

        $httpClient = $this->authenticator->getClient();
        $accessTokenValue = $this->getAuthenticator()
            ->getAccessToken()
            ->getValue();
        $fronteggConfig = $this->authenticator->getConfig();
        $url = $fronteggConfig->getServiceUrl(
            Config::AUDITS_SERVICE
        );
        $headers = [
            'Content-Type' => 'application/json',
            'x-access-token' => $accessTokenValue,
            'frontegg-tenant-id' => $tenantId,
        ];

        $lastResponse = $httpClient->send(
            $url,
            RequestInterface::METHOD_POST,
            json_encode($auditLog),
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (
            !in_array(
                $lastResponse->getHttpResponseCode(),
                [ResponseInterface::HTTP_STATUS_OK, ResponseInterface::HTTP_STATUS_ACCEPTED]
            )
        ) {
            throw new AuthenticationException($lastResponse->getBody());
        }

        $auditLogData = $this->getDecodedJsonData($lastResponse->getBody());

        if (null === $auditLogData) {
            throw new FronteggSDKException('An error occurred while response data was decoding');
        }

        return $auditLogData;
    }

    /**
     * Returns JSON data decoded into array.
     *
     * @param string|null $jsonData
     *
     * @return array|null
     */
    protected function getDecodedJsonData(?string $jsonData): ?array
    {
        if (empty($jsonData)) {
            $this->apiError = new ApiError(
                'Invalid JSON',
                'An empty string can\'t be parsed as valid JSON.'
            );

            return null;
        }

        try {
            return json_decode(
                $jsonData,
                true,
                self::JSON_DECODE_DEPTH,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            $this->apiError = new ApiError('Invalid JSON', $e->getMessage());
        }

        return null;
    }

    /**
     * @return ApiError|null
     */
    public function getApiError(): ?ApiError
    {
        return $this->apiError;
    }
}
