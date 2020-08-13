<?php

namespace Frontegg\Tests\Authenticator;

use DateTime;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggGuzzleHttpClient;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testClientCredentialsAreSet(): void
    {
        // Arrange
        $client = $this->createSuccessFronteggGuzzleHttpClientStub();
        $authenticator = $this->createFronteggAuthenticator($client, 'clientTestID', 'apiTestSecretKey');

        // Assert
        $this->assertEquals( 'clientTestID', $authenticator->getConfig()->getClientId());
        $this->assertEquals('apiTestSecretKey', $authenticator->getConfig()->getClientSecret());
    }

    /**
     * @return void
     */
    public function testAuthenticationIsWorking(): void
    {
        // Arrange
        $client = $this->createSuccessFronteggGuzzleHttpClientStub();
        $authenticator = $this->createFronteggAuthenticator($client, 'clientTestID', 'apiTestSecretKey');

        // Act
        $authenticator->authenticate();

        // Assert
        $this->assertNotNull($authenticator->getAccessToken());
        $this->assertGreaterThan(
            (new DateTime())->getTimestamp(),
            $authenticator->getAccessToken()
                ->getExpiresAt()
                ->getTimestamp()
        );
    }

    /**
     * @return void
     */
    public function testAuthenticationGetsExpiredToken(): void
    {
        // Arrange
        $client = $this->createSuccessFronteggGuzzleHttpClientStub('test token', 0);
        $authenticator = $this->createFronteggAuthenticator($client, 'clientTestID', 'apiTestSecretKey');

        // Act
        $authenticator->authenticate();

        // Assert
        $this->assertNotNull($authenticator->getAccessToken());
        $this->assertLessThanOrEqual(
            (new DateTime())->getTimestamp(),
            $authenticator->getAccessToken()
                ->getExpiresAt()
                ->getTimestamp()
        );
        $this->assertFalse($authenticator->getAccessToken()->isValid());
    }

    /**
     * @return void
     */
    public function testAuthenticationIsNotWorking(): void
    {
        // Arrange
        $client = $this->createFailureFronteggGuzzleHttpClientStub();
        $authenticator = $this->createFronteggAuthenticator($client, 'clientTestID', 'apiTestSecretKey');

        // Act
        $authenticator->authenticate();

        // Assert
        $this->assertEquals(401, $authenticator->getLastResponse()->getHttpResponseCode());
        $this->assertNull($authenticator->getAccessToken());
        $this->assertEquals('Unauthorized', $authenticator->getError()['error']);
    }

    /**
     * @return void
     */
    public function testAuthenticationValidationIsWorking(): void
    {
        // Arrange
        $client = $this->createSuccessFronteggGuzzleHttpClientStubForAuthValidation();
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            'clientTestID',
            'apiTestSecretKey'
        );
        $authenticator->authenticate();

        // Act
        $authenticator->validateAuthentication();

        // Assert
        $this->assertNotNull($authenticator->getAccessToken());
        $this->assertGreaterThan(
            (new DateTime())->getTimestamp(),
            $authenticator->getAccessToken()
                ->getExpiresAt()
                ->getTimestamp()
        );
    }

    /**
     * @return void
     */
    public function testAuthenticationValidationIsNotWorking(): void
    {
        // Arrange
        $client = $this->createFailureFronteggGuzzleHttpClientStubForAuthValidation(
            401,
            'Unauthorized'
        );
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            'clientTestID',
            'apiTestSecretKey'
        );
        $authenticator->authenticate();

        // Act
        $authenticator->validateAuthentication();

        // Assert
        $this->assertEquals(401, $authenticator->getLastResponse()->getHttpResponseCode());
        $this->assertNull($authenticator->getAccessToken());
        $this->assertEquals('Unauthorized', $authenticator->getError()['error']);
    }

    /**
     * @param FronteggGuzzleHttpClient $client
     * @param string $clientId
     * @param string $clientSecret
     * @param string $baseUrl
     * @param array $urls
     *
     * @return Authenticator
     */
    protected function createFronteggAuthenticator(
        FronteggGuzzleHttpClient $client,
        string $clientId,
        string $clientSecret,
        string $baseUrl = 'http://test',
        array $urls = []
    ): Authenticator {
        $fronteggConfig = new Config($clientId, $clientSecret, $baseUrl, $urls);

        return new Authenticator($fronteggConfig, $client);
    }

    /**
     * @param string $accessToken
     * @param int    $expiresIn Seconds to token expiration
     * @param int    $httpStatusCode
     *
     * @return Stub|FronteggGuzzleHttpClient
     */
    protected function createSuccessFronteggGuzzleHttpClientStub(
        string $accessToken = 'YOUR-JWT-TOKEN',
        int $expiresIn = 1800,
        int $httpStatusCode = 200
    ): Stub {
        $client = $this->createStub(FronteggGuzzleHttpClient::class);
        $client->method('send')
            ->willReturn(new ApiRawResponse(
                [],
                sprintf('{
                    "token": "%s",
                    "expiresIn": %d
                }', $accessToken, $expiresIn),
                $httpStatusCode
            ));

        return $client;
    }

    /**
     * @param int    $statusCode
     * @param string $error
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return Stub|FronteggGuzzleHttpClient
     */
    protected function createFailureFronteggGuzzleHttpClientStub(
        int $statusCode = 401,
        string $error = 'Unauthorized',
        string $message = 'Could not verify vendor',
        int $httpStatusCode = 401
    ): Stub {
        $client = $this->createStub(FronteggGuzzleHttpClient::class);
        $client->method('send')
            ->willReturn(new ApiRawResponse(
                [],
                sprintf('{
                    "statusCode": %s,
                    "error": "%s",
                    "message": "%s"
                }', $statusCode, $error, $message),
                $httpStatusCode
            ));

        return $client;
    }

    /**
     * @param string $accessToken
     * @param int    $expiresInForValidation Seconds to token expiration.
     *
     * @return Stub|FronteggGuzzleHttpClient
     */
    protected function createSuccessFronteggGuzzleHttpClientStubForAuthValidation(
        string $accessToken = 'YOUR-JWT-TOKEN',
        int $expiresInForValidation = 1800
    ): Stub {
        $client = $this->createStub(FronteggGuzzleHttpClient::class);
        $client->method('send')
            ->willReturnOnConsecutiveCalls(
                new ApiRawResponse(
                    [],
                    sprintf('{
                        "token": "%s",
                        "expiresIn": 0
                    }', $accessToken),
                    200
                ),
                new ApiRawResponse(
                    [],
                    sprintf('{
                        "token": "%s",
                        "expiresIn": %d
                    }', $accessToken, $expiresInForValidation),
                    200
                )
            );

        return $client;
    }

    /**
     * @param int    $statusCode
     * @param string $error
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return Stub|FronteggGuzzleHttpClient
     */
    protected function createFailureFronteggGuzzleHttpClientStubForAuthValidation(
        int $statusCode = 401,
        string $error = 'Unauthorized',
        string $message = 'Could not verify vendor',
        int $httpStatusCode = 401
    ): Stub {
        $client = $this->createStub(FronteggGuzzleHttpClient::class);
        $client->method('send')
            ->willReturnOnConsecutiveCalls(
                new ApiRawResponse(
                    [],
                    '{
                        "token": "YOUR-JWT-TOKEN",
                        "expiresIn": 0
                    }',
                    200
                ),
                new ApiRawResponse(
                    [],
                    sprintf('{
                        "statusCode": %s,
                        "error": "%s",
                        "message": "%s"
                    }', $statusCode, $error, $message),
                    $httpStatusCode
                )
            );

        return $client;
    }
}