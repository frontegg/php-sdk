<?php

namespace Frontegg\Event;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Event\Type\ChannelsConfigInterface;
use Frontegg\Event\Type\TriggerOptionsInterface;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Exception\FronteggSDKException;
use Frontegg\Exception\InvalidParameterException;
use Frontegg\Http\RequestInterface;
use Frontegg\Http\ResponseInterface;

class EventsClient
{
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

    public function trigger(TriggerOptionsInterface $triggerOptions)
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

        return $lastResponse;

        if (!in_array(
            $lastResponse->getHttpResponseCode(),
            [ResponseInterface::HTTP_STATUS_OK, ResponseInterface::HTTP_STATUS_ACCEPTED]
        )) {
            throw new AuthenticationException($lastResponse->getBody());
        }

        $auditLogData = $this->getDecodedJsonData($lastResponse->getBody());

        if (null === $auditLogData) {
            throw new FronteggSDKException('An error occurred while response data was decoding');
        }

        return $auditLogData;
    }
}