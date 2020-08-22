<?php

namespace Frontegg\Tests\Events\Type;

use Frontegg\Events\Type\SerializableInterface;
use Frontegg\Events\Type\WebPushProperties;
use Frontegg\Events\Type\WebPushPropertiesInterface;
use PHPUnit\Framework\TestCase;

class WebPushPropertiesTest extends TestCase
{
    /**
     * @return void
     */
    public function testWebPushPropertiesCanBeCreated(): void
    {
        // Act
        $object = new WebPushProperties(
            'Some test title',
            'Information data. Message number one!',
            'Test-user-ID'
        );

        // Assert
        $this->assertInstanceOf(WebPushPropertiesInterface::class, $object);
        $this->assertEquals('Some test title', $object->getTitle());
        $this->assertEquals('Information data. Message number one!', $object->getBody());
        $this->assertEquals('Test-user-ID', $object->getUserId());
    }

    /**
     * @return void
     */
    public function testWebPushPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new WebPushProperties(
            'Some test title',
            'Information data. Message number one!',
            'Test-user-ID'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "title": "Some test title",
                "body": "Information data. Message number one!",
                "userId": "Test-user-ID" 
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyWebPushPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new WebPushProperties();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "title": null,
                "body": null,
                "userId": null 
            }',
            $json
        );
    }
}
