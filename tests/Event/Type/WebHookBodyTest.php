<?php

namespace Frontegg\Tests\Event\Type;

use Frontegg\Event\Type\SerializableInterface;
use Frontegg\Event\Type\WebHookBody;
use PHPUnit\Framework\TestCase;

class WebHookBodyTest extends TestCase
{
    /**
     * @return void
     */
    public function testWebhookBodyCanBeCreated(): void
    {
        // Act
        $object = new WebHookBody(
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        // Assert
        $this->assertInstanceOf(WebHookBody::class, $object);
        $this->assertCount(3, $object->getData());
        $this->assertArrayHasKey('field 1', $object->getData());
        $this->assertEquals('value 1', $object->getValue('field 1'));
        $this->assertArrayHasKey('field 2', $object->getData());
        $this->assertEquals('value 2', $object->getValue('field 2'));
        $this->assertArrayHasKey('field 3', $object->getData());
        $this->assertEquals('value 3', $object->getValue('field 3'));
    }

    /**
     * @return void
     */
    public function testWebHookBodyCanBeSerialized(): void
    {
        // Arrange
        $object = new WebHookBody(
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "field 1": "value 1",
                "field 2": "value 2",
                "field 3": "value 3" 
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyWebHookBodyCanBeSerialized(): void
    {
        // Arrange
        $object = new WebHookBody();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{}',
            $json
        );
    }
}
