<?php

namespace Frontegg\Tests\Authenticator;

use Frontegg\Authenticator\ApiError;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /**
     * @return void
     */
    public function testApiErrorDataIsSet(): void
    {
        // Arrange
        $apiError = new ApiError('Test error', 'Error message with details', 401);

        // Assert
        $this->assertEquals(401, $apiError->getStatusCode());
        $this->assertEquals('Test error', $apiError->getError());
        $this->assertEquals(
            'Error message with details',
            $apiError->getMessage()
        );
    }
}
