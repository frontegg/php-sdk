<?php

namespace Frontegg\Tests\Events;

use Frontegg\Error\ApiError;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Events\Config\ChannelsConfig;
use Frontegg\Events\Config\DefaultProperties;
use Frontegg\Events\Config\TriggerOptions;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Tests\Helper\EventsTestCaseHelper;

class EventsClientTest extends EventsTestCaseHelper
{
    /**
     * @return void
     */
    public function testEventsClientCanTriggerEvent(): void
    {
        // Arrange
        $eventApiResponse = new ApiRawResponse(
            [],
            '{
                "eventKey":"event-key",
                "properties":{},
                "channels":{},
                "vendorId":"THE-VENDOR-ID",
                "tenantId":"THE-TENANT-ID"
            }',
            200
        );
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthHttpApiRawResponse(), $eventApiResponse]
        );
        $eventsClient = $this->createFronteggEventsClient($httpClient);

        $webhookBody = new WebHookBody(
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        $channelsConfiguration = new ChannelsConfig();
        $channelsConfiguration->setWebhook($webhookBody);

        $triggerOptions = new TriggerOptions(
            'event-key',
            new DefaultProperties(
                'Default notification title',
                'Default notification description!'
            ),
            $channelsConfiguration,
            'THE-TENANT-ID'
        );

        // Act
        $isSuccess = $eventsClient->trigger($triggerOptions);

        // Assert
        $this->assertTrue($isSuccess);
        $this->assertNull($eventsClient->getApiError());
    }

    /**
     * @return void
     */
    public function testEventsClientTriggeringEventFailed(): void
    {
        // Arrange
        $eventApiResponse = new ApiRawResponse(
            [],
            '{
                "statusCode":400,
                "message":[
                    "metadata should not be empty",
                    "channels must contain at least 1 elements",
                    "each value in channels must be a string",
                    "channels must be an array"
                ],
                "error":"Bad Request"
            }',
            400
        );
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthHttpApiRawResponse(), $eventApiResponse]
        );
        $eventsClient = $this->createFronteggEventsClient($httpClient);

        $webhookBody = new WebHookBody(
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        $channelsConfiguration = new ChannelsConfig();
        $channelsConfiguration->setWebhook($webhookBody);

        $triggerOptions = new TriggerOptions(
            'event-key',
            new DefaultProperties(
                'Default notification title',
                'Default notification description!'
            ),
            $channelsConfiguration,
            'THE-TENANT-ID'
        );

        // Act
        $isSuccess = $eventsClient->trigger($triggerOptions);

        // Assert
        $this->assertFalse($isSuccess);
        $this->assertInstanceOf(ApiError::class, $eventsClient->getApiError());
    }
}
