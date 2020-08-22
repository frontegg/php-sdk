<?php

namespace Frontegg\Tests\Event\Type;

use Frontegg\Event\Type\ChannelsConfig;
use Frontegg\Event\Type\ChannelsConfigInterface;
use Frontegg\Event\Type\DefaultProperties;
use Frontegg\Event\Type\DefaultPropertiesInterface;
use Frontegg\Event\Type\SerializableInterface;
use Frontegg\Event\Type\TriggerOptions;
use Frontegg\Event\Type\TriggerOptionsInterface;
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
            new ChannelsConfig(),
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
                    "webpush": true,
                    "bell": true,
                    "audit": true,
                    "slack": true
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
            new ChannelsConfig()
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
                    "webpush": true,
                    "bell": true,
                    "audit": true,
                    "slack": true
                }
            }',
            $json
        );
    }
}
