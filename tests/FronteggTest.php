<?php

namespace Frontegg\Tests;

use Frontegg\Authenticator\AccessToken;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Frontegg;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class FronteggTest extends TestCase
{
    /**
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testFronteggAuthenticatorIsCreated(): void
    {
        // Arrange
        $config = [
            'clientId' => 'clientTestID',
            'clientSecret' => 'apiTestSecretKey',
        ];
        $frontegg = new Frontegg($config);

        // Assert
        $this->assertInstanceOf(Authenticator::class, $frontegg->getAuthenticator());
        $this->assertInstanceOf(Config::class, $frontegg->getConfig());
        $this->assertEquals('clientTestID', $frontegg->getConfig()->getClientId());
        $this->assertEquals('apiTestSecretKey', $frontegg->getConfig()->getClientSecret());
        $this->assertInstanceOf(FronteggHttpClientInterface::class, $frontegg->getClient());
    }

    /**
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testFronteggInitialized(): void
    {
        // Arrange
        $httpClient = $this->createSuccessFronteggCurlHttpClientStub();
        $config = [
            'clientId' => 'clientTestID',
            'clientSecret' => 'apiTestSecretKey',
            'httpClientHandler' => $httpClient,
        ];
        $frontegg = new Frontegg($config);

        // Act
        $frontegg->init();

        // Assert
        $this->assertInstanceOf(Authenticator::class, $frontegg->getAuthenticator());
        $this->assertInstanceOf(
            AccessToken::class,
            $frontegg->getAuthenticator()->getAccessToken()
        );
    }

    /**
     * @param string $accessToken
     * @param int    $expiresIn Seconds to token expiration
     * @param int    $httpStatusCode
     *
     * @return Stub|FronteggCurlHttpClient
     */
    protected function createSuccessFronteggCurlHttpClientStub(
        string $accessToken = 'YOUR-JWT-TOKEN',
        int $expiresIn = 1800,
        int $httpStatusCode = 200
    ): Stub {
        $client = $this->createStub(FronteggCurlHttpClient::class);
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
}