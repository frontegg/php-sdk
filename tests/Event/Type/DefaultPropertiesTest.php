<?php

namespace Frontegg\Tests\Event\Type;

use Frontegg\Event\Type\DefaultProperties;
use Frontegg\Event\Type\DefaultPropertiesInterface;
use Frontegg\Event\Type\SerializableInterface;
use PHPUnit\Framework\TestCase;

class DefaultPropertiesTest extends TestCase
{
    /**
     * @return void
     */
    public function testDefaultPropertiesCanBeCreated(): void
    {
        // Act
        $object = new DefaultProperties(
            'this is title',
            'This is full description!',
            [
                'field 1' => 'some additional string 1',
                'field 2' => 'some additional string 2',
                'field 3' => 'some additional string 3',
            ]
        );

        // Assert
        $this->assertInstanceOf(
            DefaultPropertiesInterface::class,
            $object
        );
        $this->assertEquals('this is title', $object->getTitle());
        $this->assertEquals('This is full description!', $object->getDescription());
        $this->assertCount(3, $object->getAdditionalProperties());
        $this->assertArrayHasKey('field 1', $object->getAdditionalProperties());
        $this->assertContains('some additional string 1', $object->getAdditionalProperties());
        $this->assertArrayHasKey('field 2', $object->getAdditionalProperties());
        $this->assertContains('some additional string 2', $object->getAdditionalProperties());
        $this->assertArrayHasKey('field 3', $object->getAdditionalProperties());
        $this->assertContains('some additional string 3', $object->getAdditionalProperties());
    }

    /**
     * @return void
     */
    public function testDefaultPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new DefaultProperties(
            'this is title',
            'This is full description!',
            [
                'field 1' => 'some additional string 1',
                'field 2' => 'some additional string 2',
                'field 3' => 'some additional string 3',
            ]
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "title": "this is title",
                "description": "This is full description!",
                "field 1": "some additional string 1",
                "field 2": "some additional string 2",
                "field 3": "some additional string 3"
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyDefaultPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new DefaultProperties(
            'this is title',
            'This is full description!'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "title": "this is title",
                "description": "This is full description!"
            }',
            $json
        );
    }
}
