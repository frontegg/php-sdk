<?php

namespace Frontegg\Tests\Events\Config;

use Frontegg\Events\Config\SerializableInterface;
use Frontegg\Events\Config\UseChannelDefaults;
use PHPUnit\Framework\TestCase;

class UseChannelDefaultsTest extends TestCase
{
    /**
     * @return void
     */
    public function testUseChannelDefaultsCanBeCreated(): void
    {
        // Act
        $object = new UseChannelDefaults();

        // Assert
        $this->assertInstanceOf(UseChannelDefaults::class, $object);
    }

    /**
     * @return void
     */
    public function testUseChannelDefaultsCanBeSerialized(): void
    {
        // Arrange
        $object = new UseChannelDefaults();

        // Act
        $json = $object->toJSON();

        // Assert
        $this->assertInstanceOf(SerializableInterface::class, $object);
        $this->assertJsonStringEqualsJsonString(
            'true',
            $json
        );
    }
}
