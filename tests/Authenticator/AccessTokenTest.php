<?php

namespace Frontegg\Tests\Authenticator;

use DateTime;
use Frontegg\Authenticator\AccessToken;
use PHPUnit\Framework\TestCase;

class AccessTokenTest extends TestCase
{
    /**
     * @return void
     */
    public function testAccessTokenIsSet(): void
    {
        // Arrange
        $accessToken = new AccessToken('ACCESS_TOKEN_VALUE', new DateTime('tomorrow'));

        // Assert
        $this->assertEquals('ACCESS_TOKEN_VALUE', $accessToken->getValue());
        $this->assertEquals((new DateTime('tomorrow'))->format('Y-m-d h:i:s'),
                            $accessToken->getExpiresAt()->format('Y-m-d h:i:s'));
    }

    /**
     * @return void
     */
    public function testAccessTokenIsValid(): void
    {
        // Arrange
        $accessToken = new AccessToken('ACCESS_TOKEN_VALUE', new DateTime('tomorrow'));

        // Assert
        $this->assertTrue($accessToken->isValid());
    }

    /**
     * @return void
     */
    public function testAccessTokenIsNotValid(): void
    {
        // Arrange
        $accessToken = new AccessToken('ACCESS_TOKEN_VALUE', new DateTime('2019-12-10 14:52:12'));

        // Assert
        $this->assertFalse($accessToken->isValid());
    }
}