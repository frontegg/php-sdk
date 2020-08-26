<?php

namespace Frontegg\Tests\Events\Channel;

use DateTime;
use Frontegg\Events\Channel\AuditProperties;
use Frontegg\Events\Channel\AuditPropertiesInterface;
use Frontegg\Events\Config\SerializableInterface;
use PHPUnit\Framework\TestCase;

class AuditPropertiesTest extends TestCase
{
    /**
     * @return void
     */
    public function testAuditPropertiesCanBeCreated(): void
    {
        // Act
        $object = new AuditProperties(
            'High',
            new DateTime(
                '2020-11-22 00:33:44'
            ),
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        // Assert
        $this->assertInstanceOf(
            AuditPropertiesInterface::class,
            $object
        );
        $this->assertEquals('High', $object->getSeverity());
        $this->assertEquals(
            new DateTime('2020-11-22 00:33:44'),
            $object->getCreatedAt()
        );
        $this->assertCount(3, $object->getFields());
        $this->assertArrayHasKey('field 1', $object->getFields());
        $this->assertContains('value 1', $object->getFields());
        $this->assertArrayHasKey('field 2', $object->getFields());
        $this->assertContains('value 2', $object->getFields());
        $this->assertArrayHasKey('field 3', $object->getFields());
        $this->assertContains('value 3', $object->getFields());
    }

    /**
     * @return void
     */
    public function testAuditPropertiesSetIncorrectSeverityValueShouldSetDefaultValueInstead(): void
    {
        // Act
        $object = new AuditProperties(
            'Something strange happened'
        );
        ;

        // Assert
        $this->assertEquals('Info', $object->getSeverity());
    }

    /**
     * @return void
     */
    public function testAuditPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new AuditProperties(
            'High',
            new DateTime(
                '2020-11-22 00:33:44'
            ),
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
                "severity": "High",
                "createdAt": "2020-11-22 00:33:44",
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
    public function testEmptyAuditPropertiesCanBeSerialized(): void
    {
        // Arrange
        $object = new AuditProperties();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            sprintf('{
                "severity": "Info",
                "createdAt": "%s"
            }', (new DateTime())->format('Y-m-d H:i:s')),
            $json
        );
    }
}
