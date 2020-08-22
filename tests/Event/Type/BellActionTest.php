<?php

namespace Frontegg\Tests\Event\Type;

use Frontegg\Event\Type\BellAction;
use Frontegg\Event\Type\BellActionInterface;
use Frontegg\Event\Type\SerializableInterface;
use Frontegg\Exception\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class BellActionTest extends TestCase
{
    /**
     * @return void
     */
    public function testBellActionCanBeCreated(): void
    {
        // Act
        $object = new BellAction(
            'Notification Action Name',
            'https://redirect.url/when/clicked',
            'OPTIONS',
            'Link'
        );

        // Assert
        $this->assertInstanceOf(BellActionInterface::class, $object);
        $this->assertEquals('Notification Action Name', $object->getName());
        $this->assertEquals('https://redirect.url/when/clicked', $object->getUrl());
        $this->assertEquals('OPTIONS', $object->getMethod());
        $this->assertEquals('Link', $object->getVisualization());
    }

    /**
     * @return void
     */
    public function testBellActionSetIncorrectVisualizationValueShouldSetDefaultValueInstead(): void
    {
        // Act
        $object = new BellAction(
            'Notification Action Name',
            'https://redirect.url/when/clicked',
            'POST',
            'Banner on the main page'
        );

        // Assert
        $this->assertEquals('Button', $object->getVisualization());
    }

    /**
     * @return void
     */
    public function testBellActionSetIncorrectMethodShouldThrowAnException(): void
    {
        // Act
        $this->expectException(InvalidParameterException::class);
        new BellAction(
            'Notification Action Name',
            'https://redirect.url/when/clicked',
            'NON-EXISTING-HTTP-METHOD',
            'Link'
        );
    }

    /**
     * @return void
     */
    public function testBellActionCanBeSerialized(): void
    {
        // Arrange
        $object = new BellAction(
            'Notification Action Name',
            'https://redirect.url/when/clicked',
            'DELETE',
            'Link'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "name": "Notification Action Name",
                "url": "https://redirect.url/when/clicked",
                "method": "DELETE",
                "visualization": "Link"
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyBellActionCanBeSerialized(): void
    {
        // Arrange
        $object = new BellAction(
            'Notification Action Name',
            'https://redirect.url/when/clicked',
            'DELETE'
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "name": "Notification Action Name",
                "url": "https://redirect.url/when/clicked",
                "method": "DELETE",
                "visualization": "Button"
            }',
            $json
        );
    }
}
