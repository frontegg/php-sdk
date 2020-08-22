<?php

namespace Frontegg\Tests\Event\Type;

use DateTime;
use Frontegg\Event\Type\BellAction;
use Frontegg\Event\Type\BellProperties;
use Frontegg\Event\Type\BellPropertiesInterface;
use Frontegg\Event\Type\SerializableInterface;
use Frontegg\Exception\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class BellPropertiesTest extends TestCase
{
    /**
     * @return void
     */
    public function testBellPropertiesCanBeCreated(): void
    {
        // Act
        $object = new BellProperties(
            'Test-user-ID',
            'Test title',
            'This is body!',
            'High',
            new DateTime('2020-11-22 00:33:44'),
            'https://open.in.the/blank/window',
            [
                new BellAction(
                    'Action Name 1',
                    'https://redirect.url/when/clicked/1',
                    'GET',
                    'Link'
                ),
                new BellAction(
                    'Action Name 2',
                    'https://redirect.url/when/clicked/2',
                    'POST',
                    'Button'
                ),
                new BellAction(
                    'Action Name 3',
                    'https://redirect.url/when/clicked/3',
                    'PUT',
                    'Button'
                ),
            ]
        );

        // Assert
        $this->assertInstanceOf(
            BellPropertiesInterface::class,
            $object
        );
        $this->assertEquals('Test-user-ID', $object->getUserId());
        $this->assertEquals('Test title', $object->getTitle());
        $this->assertEquals('This is body!', $object->getBody());
        $this->assertEquals('High', $object->getSeverity());
        $this->assertEquals(
            new DateTime('2020-11-22 00:33:44'),
            $object->getExpiryDate()
        );
        $this->assertEquals(
            'https://open.in.the/blank/window',
            $object->getUrl()
        );
        $this->assertCount(3, $object->getActions());

        $this->assertEquals(
            'Action Name 1',
            $object->getActions()[0]->getName()
        );
        $this->assertEquals(
            'https://redirect.url/when/clicked/1',
            $object->getActions()[0]->getUrl()
        );
        $this->assertEquals(
            'GET',
            $object->getActions()[0]->getMethod()
        );
        $this->assertEquals(
            'Link',
            $object->getActions()[0]->getVisualization()
        );

        $this->assertEquals(
            'Action Name 2',
            $object->getActions()[1]->getName()
        );
        $this->assertEquals(
            'https://redirect.url/when/clicked/2',
            $object->getActions()[1]->getUrl()
        );
        $this->assertEquals(
            'POST',
            $object->getActions()[1]->getMethod()
        );
        $this->assertEquals(
            'Button',
            $object->getActions()[1]->getVisualization()
        );

        $this->assertEquals(
            'Action Name 3',
            $object->getActions()[2]->getName()
        );
        $this->assertEquals(
            'https://redirect.url/when/clicked/3',
            $object->getActions()[2]->getUrl()
        );
        $this->assertEquals(
            'PUT',
            $object->getActions()[2]->getMethod()
        );
        $this->assertEquals(
            'Button',
            $object->getActions()[2]->getVisualization()
        );
    }

    /**
     * @return void
     */
    public function testBellPropertiesSetIncorrectSeverityValueShouldSetDefaultValueInstead(): void
    {
        // Act
        $object = new BellProperties(
            null,
            null,
            null,
            'Something very critical!'
        );

        // Assert
        $this->assertEquals('Info', $object->getSeverity());
    }

    /**
     * @return void
     */
    public function testBellPropertiesSetIncorrectActionsShouldThrowAnException(): void
    {
        // Act
        $this->expectException(InvalidParameterException::class);
        new BellProperties(
            null,
            null,
            null,
            null,
            null,
            null,
            [
                new BellAction(
                    'Action Name 1',
                    'https://redirect.url/when/clicked/1',
                    'GET',
                    'Link'
                ),
                new DateTime(),
                new DateTime(),
            ]
        );
    }

    /**
     * @return void
     */
    public function testBellPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new BellProperties(
            'Test-user-ID',
            'Test title',
            'This is body!',
            'High',
            new DateTime('2020-11-22 00:33:44'),
            'https://open.in.the/blank/window',
            [
                new BellAction(
                    'Action Name 1',
                    'https://redirect.url/when/clicked/1',
                    'GET',
                    'Link'
                ),
                new BellAction(
                    'Action Name 2',
                    'https://redirect.url/when/clicked/2',
                    'POST',
                    'Button'
                ),
                new BellAction(
                    'Action Name 3',
                    'https://redirect.url/when/clicked/3',
                    'PUT',
                    'Button'
                ),
            ]
        );

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "userId": "Test-user-ID",
                "title": "Test title",
                "body": "This is body!",
                "severity": "High",
                "expiryDate": "2020-11-22 00:33:44",
                "url": "https://open.in.the/blank/window",
                "actions": [
                    {
                        "name": "Action Name 1",
                        "url": "https://redirect.url/when/clicked/1",
                        "method": "GET",
                        "visualization": "Link"
                    },
                    {
                        "name": "Action Name 2",
                        "url": "https://redirect.url/when/clicked/2",
                        "method": "POST",
                        "visualization": "Button"
                    },
                    {
                        "name": "Action Name 3",
                        "url": "https://redirect.url/when/clicked/3",
                        "method": "PUT",
                        "visualization": "Button"
                    }
                ]
            }',
            $json
        );
    }

    /**
     * @return void
     */
    public function testEmptyBellPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new BellProperties();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            '{
                "userId": null,
                "title": null,
                "body": null,
                "severity": "Info",
                "expiryDate": null,
                "url": null,
                "actions": {}
            }',
            $json
        );
    }
}
