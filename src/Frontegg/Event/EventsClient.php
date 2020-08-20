<?php

namespace Frontegg\Event;

use Frontegg\Authenticator\ApiError;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Event\Type\TriggerOptionsInterface;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\Exception\InvalidParameterException;
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
     *
     * @param TriggerOptionsInterface $triggerOptions
     *
     * @throws AuthenticationException
     * @throws FronteggSDKException
     * @throws InvalidParameterException
     * @throws \Frontegg\Exception\InvalidUrlConfigException
     *
     * @return array
     */
    public function trigger(TriggerOptionsInterface $triggerOptions): array
    {
        if (!$triggerOptions->getChannels()->isConfigured()) {
            throw new InvalidParameterException(
                'At least one channel should be configured'
            );
        }

        $this->authenticator->validateAuthentication();

        // @todo: Refactor this.

        $httpClient = $this->authenticator->getClient();
        $accessTokenValue = $this->authenticator
            ->getAccessToken()
            ->getValue();
        $fronteggConfig = $this->authenticator->getConfig();
        $url = $fronteggConfig->getServiceUrl(
            Config::SERVICE_AUDITS
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
                'properties' => $triggerOptions->getDefaultProperties(),
                'channels' => $triggerOptions->getChannels(),
            ]),
            $headers,
            RequestInterface::HTTP_REQUEST_TIMEOUT
        );

        if (!in_array(
            $lastResponse->getHttpResponseCode(),
            [ResponseInterface::HTTP_STATUS_OK, ResponseInterface::HTTP_STATUS_ACCEPTED]
        )) {
            throw new AuthenticationException($lastResponse->getBody());
        }

        $triggeredEventData = $this->getDecodedJsonData($lastResponse->getBody());

        if (null === $triggeredEventData) {
            throw new FronteggSDKException('An error occurred while response data was decoding');
        }

        return $triggeredEventData;
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
}