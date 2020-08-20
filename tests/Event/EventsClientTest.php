<?php

namespace Frontegg\Tests\Event;

use Frontegg\Event\Type\ChannelsConfig;
use Frontegg\Event\Type\DefaultProperties;
use Frontegg\Event\Type\TriggerOptions;
use Frontegg\Event\Type\WebHookBody;
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
        '{data: Success}',
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
        $response = $eventsClient->trigger($triggerOptions);

        // Assert
        var_dump($response);
        $this->assertNotNull($response['data']);
    }
}