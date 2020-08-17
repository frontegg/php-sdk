<?php

namespace Frontegg\Tests;

use Frontegg\Authenticator\AccessToken;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Frontegg;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Tests\Helper\AuthenticatorTestCaseHelper;

class FronteggTest extends AuthenticatorTestCaseHelper
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
        $this->assertInstanceOf(
            Authenticator::class,
            $frontegg->getAuthenticator()
        );
        $this->assertInstanceOf(Config::class, $frontegg->getConfig());
        $this->assertEquals(
            'clientTestID',
            $frontegg->getConfig()->getClientId()
        );
        $this->assertEquals(
            'apiTestSecretKey',
            $frontegg->getConfig()->getClientSecret()
        );
        $this->assertInstanceOf(
            FronteggHttpClientInterface::class,
            $frontegg->getClient()
        );
    }

    /**
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testFronteggInitialized(): void
    {
        // Arrange
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$this->createAuthHttpApiRawResponse()]
        );
        $config = [
            'clientId' => 'clientTestID',
            'clientSecret' => 'apiTestSecretKey',
            'httpClientHandler' => $httpClient,
        ];
        $frontegg = new Frontegg($config);

        // Act
        $frontegg->init();

        // Assert
        $this->assertInstanceOf(
            Authenticator::class,
            $frontegg->getAuthenticator()
        );
        $this->assertInstanceOf(
            AccessToken::class,
            $frontegg->getAuthenticator()->getAccessToken()
        );
    }

    /**
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testFronteggGetAudits(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $auditsResponse = new ApiRawResponse(
            [],
            json_encode(
                [
                    'data' => [
                        ['log1'],
                        ['log 2'],
                    ],
                    'total' => 2,
                ]
            ),
            200
        );
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$authResponse, $auditsResponse]
        );
        $config = [
            'clientId' => 'clientTestID',
            'clientSecret' => 'apiTestSecretKey',
            'httpClientHandler' => $httpClient,
        ];
        $frontegg = new Frontegg($config);

        // Act
        $auditLogs = $frontegg->getAudits('THE-TENANT-ID');

        // Assert
        $this->assertNotEmpty($auditLogs['data']);
        $this->assertGreaterThanOrEqual(2, count($auditLogs['data']));
        $this->assertNotEmpty($auditLogs['total']);
        $this->assertContains(['log1'], $auditLogs['data']);
        $this->assertContains(['log 2'], $auditLogs['data']);
    }
}
