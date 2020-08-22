<?php

namespace Frontegg\Tests\Events\Type;

use ArrayObject;
use DateTime;
use Frontegg\Events\Type\AuditProperties;
use Frontegg\Events\Type\AuditPropertiesInterface;
use Frontegg\Events\Type\BellAction;
use Frontegg\Events\Type\BellProperties;
use Frontegg\Events\Type\BellPropertiesInterface;
use Frontegg\Events\Type\ChannelsConfig;
use Frontegg\Events\Type\ChannelsConfigInterface;
use Frontegg\Events\Type\SerializableInterface;
use Frontegg\Events\Type\SlackChatPostMessageArguments;
use Frontegg\Events\Type\SlackChatPostMessageArgumentsInterface;
use Frontegg\Events\Type\WebHookBody;
use Frontegg\Events\Type\WebPushProperties;
use Frontegg\Events\Type\WebPushPropertiesInterface;
use PHPUnit\Framework\TestCase;

class ChannelsConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testChannelsConfigurationCanBeCreated(): void
    {
        // Arrange
        $object = new ChannelsConfig();
        $object->setWebHook(
            new WebHookBody(
                [
                    'field 1' => 'value 1',
                    'field 2' => 'value 2',
                    'field 3' => 'value 3',
                ]
            )
        );
        $object->setWebPush(
            new WebPushProperties(
                'Some test title',
                'Information data. Message number one!',
                'Test-user-ID'
            )
        );
        $object->setAudit(
            new AuditProperties(
                'High',
                new DateTime(
                    '2020-11-22 00:33:44'
                ),
                [
                    'field 1' => 'value 1',
                    'field 2' => 'value 2',
                    'field 3' => 'value 3',
                ]
            )
        );
        $object->setBell(
            new BellProperties(
                null,
                null,
                null,
                'High',
                null,
                null,
                [
                    new BellAction(
                        'Action Name 1',
                        'https://redirect.url/when/clicked/1',
                        'GET',
                        'Link'
                    ),
                ]
            )
        );
        $object->setSlack(
            new SlackChatPostMessageArguments(
                'SLACK-API-TOKEN',
                '#general',
                'Some text to show!'
            )
        );

        // Assert
        $this->assertInstanceOf(
            ChannelsConfigInterface::class,
            $object
        );
        $this->assertInstanceOf(
            WebHookBody::class,
            $object->getWebHook()
        );
        $this->assertInstanceOf(
            WebPushPropertiesInterface::class,
            $object->getWebPush()
        );
        $this->assertInstanceOf(
            AuditPropertiesInterface::class,
            $object->getAudit()
        );
        $this->assertInstanceOf(
            BellPropertiesInterface::class,
            $object->getBell()
        );
        $this->assertInstanceOf(
            SlackChatPostMessageArgumentsInterface::class,
            $object->getSlack()
        );
    }

    /**
     * @return void
     */
    public function testChannelsConfigCanBeSerialized(): void
    {
        // Arrange
        $object = new ChannelsConfig();
        $object->setWebHook(
            new WebHookBody(
                [
                    'field 1' => 'value 1',
                    'field 2' => 'value 2',
                    'field 3' => 'value 3',
                ]
            )
        );
        $object->setWebPush(
            new WebPushProperties(
                'Some test title',
                'Information data. Message number one!',
                'Test-user-ID'
            )
        );
        $object->setAudit(
            new AuditProperties(
                'High',
                new DateTime(
                    '2020-11-22 00:33:44'
                ),
                [
                    'field 1' => 'value 1',
                    'field 2' => 'value 2',
                    'field 3' => 'value 3',
                ]
            )
        );
        $object->setBell(
            new BellProperties(
                null,
                null,
                null,
                'High',
                null,
                null,
                [
                    new BellAction(
                        'Action Name 1',
                        'https://redirect.url/when/clicked/1',
                        'GET',
                        'Link'
                    ),
                ]
            )
        );
        $object->setSlack(
            new SlackChatPostMessageArguments(
                'SLACK-API-TOKEN',
                '#general',
                'Some text to show!'
            )
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "webhook": {
                    "field 1": "value 1",
                    "field 2": "value 2",
                    "field 3": "value 3"
                },
                "webpush": {
                    "title": "Some test title",
                    "body": "Information data. Message number one!",
                    "userId": "Test-user-ID"
                },
                "bell": {
                    "userId": null,
                    "title": null,
                    "body": null,
                    "severity": "High",
                    "expiryDate": null,
                    "url": null,
                    "actions": [
                        {
                            "name": "Action Name 1",
                            "url": "https://redirect.url/when/clicked/1",
                            "method": "GET",
                            "visualization": "Link"
                        }
                    ]
                },
                "audit": {
                    "severity": "High",
                    "createdAt": "2020-11-22 00:33:44",
                    "field 1": "value 1",
                    "field 2": "value 2",
                    "field 3": "value 3"
                },
                "slack": {
                    "token": "SLACK-API-TOKEN",
                    "channel": "#general",
                    "text": "Some text to show!",
                    "as_user": null,
                    "attachments": {},
                    "blocks": {},
                    "icon_emoji": null,
                    "icon_url": null,
                    "link_names": null,
                    "mrkdwn": null,
                    "parse": "none",
                    "reply_broadcast": null,
                    "thread_ts": null,
                    "unfurl_links": null,
                    "unfurl_media": null,
                    "username": null
                }
            }',
            $json
        );
        $this->assertTrue($object->isConfigured());
    }

    /**
     * @return void
     */
    public function testEmptyChannelsConfigCanBeSerialized(): void
    {
        // Arrange
        $object = new ChannelsConfig();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "webhook": true,
                "webpush": true,
                "bell": true,
                "audit": true,
                "slack": true
            }',
            $json
        );
        $this->assertFalse($object->isConfigured());
    }
}