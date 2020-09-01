<?php

namespace Frontegg\Tests\Events\Config;

use Frontegg\Events\Config\ChannelsConfig;
use Frontegg\Events\Config\ChannelsConfigInterface;
use Frontegg\Events\Config\DefaultProperties;
use Frontegg\Events\Config\DefaultPropertiesInterface;
use Frontegg\Events\Config\SerializableInterface;
use Frontegg\Events\Config\TriggerOptions;
use Frontegg\Events\Config\TriggerOptionsInterface;
use Frontegg\Events\Config\UseChannelDefaults;
use PHPUnit\Framework\TestCase;

class TriggerOptionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testTriggerOptionsCanBeCreated(): void
    {
        // Act
        $object = new TriggerOptions(
            'event-key-for-test',
            new DefaultProperties(
                'Default title',
                'Default description'
            ),
            new ChannelsConfig(),
            'THE-TENANT-ID'
        );

        // Assert
        $this->assertInstanceOf(
            TriggerOptionsInterface::class,
            $object
        );
        $this->assertEquals('event-key-for-test', $object->getEventKey());
        $this->assertInstanceOf(DefaultPropertiesInterface::class, $object->getDefaultProperties());
        $this->assertEquals('THE-TENANT-ID', $object->getTenantId());
        $this->assertInstanceOf(ChannelsConfigInterface::class, $object->getChannels());
    }

    /**
     * @return void
     */
    public function testTriggerOptionsCanBeSerialized(): void
    {
        // Arrange
        $object = new TriggerOptions(
            'event-key-for-test',
            new DefaultProperties(
                'Default title',
                'Default description'
            ),
            new ChannelsConfig(
                new UseChannelDefaults(),
                null,
                new UseChannelDefaults()
            ),
            'THE-TENANT-ID'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "eventKey": "event-key-for-test",
                "properties": {
                    "title": "Default title",
                    "description": "Default description"
                },
                "tenantId": "THE-TENANT-ID",
                "channels": {
                    "webhook": true,
                    "audit": true
                }
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyTriggerOptionsCanBeSerialized(): void
    {
        // Arrange
        $object = new TriggerOptions(
            'event-key-for-test',
            new DefaultProperties(
                'Default title',
                'Default description'
            ),
            new ChannelsConfig(
                new UseChannelDefaults(),
                null,
                null,
                null,
                new UseChannelDefaults()
            )
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "eventKey": "event-key-for-test",
                "properties": {
                    "title": "Default title",
                    "description": "Default description"
                },
                "tenantId": null,
                "channels": {
                    "webhook": true,
                    "slack": true
                }
            }',
            $json
        );
    }
}
