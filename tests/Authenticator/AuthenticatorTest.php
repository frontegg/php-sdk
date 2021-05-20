<?php

namespace Frontegg\Tests\Authenticator;

use DateTime;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Frontegg\Tests\Helper\AuthenticatorTestCaseHelper;
use Prophecy\Argument;

class AuthenticatorTest extends AuthenticatorTestCaseHelper
{
    /**
     * @return void
     */
    public function testClientCredentialsAreSet(): void
    {
        // Arrange
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthHttpApiRawResponse()]
        );
        $authenticator = $this->createFronteggAuthenticator($httpClient);

        // Assert
        $this->assertEquals(
            'clientTestID',
            $authenticator->getConfig()->getClientId()
        );
        $this->assertEquals(
            'apiTestSecretKey',
            $authenticator->getConfig()->getClientSecret()
        );
    }

    /**
     * @return void
     */
    public function testAuthenticationIsWorking(): void
    {
        // Arrange
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthHttpApiRawResponse()]
        );
        $authenticator = $this->createFronteggAuthenticator($httpClient);

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
        $authResponse = $this->createAuthHttpApiRawResponse(
            'test token',
            0
        );
        $authenticator = $this->createFronteggAuthenticator(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse]
            )
        );

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
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthFailureHttpApiRawResponse()]
        );
        $authenticator = $this->createFronteggAuthenticator($httpClient);

        // Act
        $authenticator->authenticate();

        // Assert
        $this->assertEquals(
            401,
            $authenticator->getLastResponse()->getHttpResponseCode()
        );
        $this->assertNull($authenticator->getAccessToken());
        $this->assertEquals(
            'Unauthorized',
            $authenticator->getApiError()->getError()
        );
    }

    /**
     * @return void
     */
    public function testAuthenticationValidationIsWorking(): void
    {
        // Arrange
        $failedAuthRequest = $this->createAuthHttpApiRawResponse('test-token', 0);
        $validateAuthRequest = $this->createAuthHttpApiRawResponse('test-token', 1800);

        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$failedAuthRequest, $validateAuthRequest]
        );
        $authenticator = $this->createFronteggAuthenticator($httpClient);

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
        $authResponse = $this->createAuthFailureHttpApiRawResponse();
        $authdValidationResponse = $this->createAuthFailureHttpApiRawResponse();

        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$authResponse, $authdValidationResponse]
        );
        $authenticator = $this->createFronteggAuthenticator($httpClient);

        $authenticator->authenticate();

        // Act
        $authenticator->validateAuthentication();

        // Assert
        $this->assertEquals(
            401,
            $authenticator->getLastResponse()->getHttpResponseCode()
        );
        $this->assertNull($authenticator->getAccessToken());
        $this->assertEquals(
            'Unauthorized',
            $authenticator->getApiError()->getError()
        );
    }

    public function testAuthenticatorCallsAuthUrl()
    {
        $authBaseUrl = 'http://authentication';

        $client = $this->prophesize(FronteggCurlHttpClient::class);
        $client->send(
            Argument::containingString($authBaseUrl),
            Argument::any(),
            Argument::any(),
            Argument::any(),
            Argument::any()
        )
            ->shouldBeCalledOnce()
            ->willReturn($this->createAuthHttpApiRawResponse());

        $authenticator = $this->createFronteggAuthenticator(
            $client->reveal(),
            'clientTestID',
            'apiTestSecretKey',
            'http://test',
            [],
            true,
            null,
            $authBaseUrl
        );
        $authenticator->authenticate();
    }
}
