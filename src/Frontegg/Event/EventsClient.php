<?php

namespace Frontegg\Event;

use Frontegg\Authenticator\ApiError;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Event\Type\TriggerOptionsInterface;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\EventTriggerException;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\Exception\InvalidParameterException;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Http\RequestInterface;
use Frontegg\Http\ResponseInterface;
use JsonException;

class EventsClient
{
    protected const JSON_DECODE_DEPTH = 512;

    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * API error.
     *
     * @var ApiError|null
     */
    protected $apiError;

    /**
     * EventsClient constructor.
     *
     * @param Authenticator $authenticator
     */
    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Trigger the event specified by trigger options.
     * Returns true on success.
     * Returns true on failure and $apiError property will contain an error.
     *
     * @param TriggerOptionsInterface $triggerOptions
     *
     * @throws EventTriggerException
     * @throws FronteggSDKException
     * @throws InvalidParameterException
     * @throws \Frontegg\Exception\InvalidUrlConfigException
     *
     * @return bool
     */
    public function trigger(TriggerOptionsInterface $triggerOptions): bool
    {
        if (!$triggerOptions->getChannels()->isConfigured()) {
            throw new InvalidParameterException(
                'At least one channel should be configured'
            );
        }

        $this->authenticator->validateAuthentication();
        if (!$this->authenticator->getAccessToken()) {
            throw new AuthenticationException('Authentication problem');
        }

        // @todo: Refactor this.

        $httpClient = $this->authenticator->getClient();
        $accessTokenValue = $this->authenticator
            ->getAccessToken()
            ->getValue();
        $fronteggConfig = $this->authenticator->getConfig();
        $url = $fronteggConfig->getServiceUrl(
            Config::SERVICE_EVENTS
        );
        $headers = [
            'Content-Type' => 'application/json',
            'x-access-token' => $accessTokenValue,
            'frontegg-tenant-id' => $triggerOptions->getTenantId(),
        ];

        $lastResponse = $httpClient->send(
            $url,
            RequestInterface::METHOD_POST,
            json_encode([
                'eventKey' => $triggerOptions->getEventKey(),
                'properties' => $triggerOptions->getDefaultProperties()->toArray(),
                'channels' => $triggerOptions->getChannels()->toArray(),
            ]),
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (
            !in_array(
                $lastResponse->getHttpResponseCode(),
                [ResponseInterface::HTTP_STATUS_OK, ResponseInterface::HTTP_STATUS_ACCEPTED]
            )
        ) {
            if (empty($lastResponse->getBody())) {
                throw new EventTriggerException($lastResponse->getBody());
            }
            $this->setErrorFromResponseData($lastResponse);

            return false;
        }

        return true;
    }

    /**
     * Sets an error data from response data.
     * Sets access token to null.
     *
     * @param ApiRawResponse $response
     *
     * @return void
     */
    protected function setErrorFromResponseData(ApiRawResponse $response): void
    {
        $errorDecoded = $this->getDecodedJsonData(
            $response->getBody()
        );

        $this->apiError = new ApiError(
            $errorDecoded['error'] ?? '',
            $errorDecoded['message'] ? print_r($errorDecoded['message'], true) : '',
            $errorDecoded['statusCode'] ?? null,
        );
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
