<?php

namespace Frontegg\Tests;

use Frontegg\Authenticator\AccessToken;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Event\Type\ChannelsConfig;
use Frontegg\Event\Type\DefaultProperties;
use Frontegg\Event\Type\TriggerOptions;
use Frontegg\Event\Type\WebHookBody;
use Frontegg\Frontegg;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Http\RequestInterface;
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
            'contextResolver' => function (RequestInterface $request) {
                return [];
            }
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
            'contextResolver' => function (RequestInterface $request) {
                return [];
            }
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
            'contextResolver' => function (RequestInterface $request) {
                return [];
            }
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

    /**
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testFronteggTriggerEvent(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $eventsResponse = new ApiRawResponse(
            [],
            '{
                "eventKey":"event-key",
                "properties":{},
                "channels":{},
                "vendorId":"6da27373-1572-444f-b3c5-ef702ce65123",
                "tenantId":"THE-TENANT-ID"
            }',
            200
        );
        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$authResponse, $eventsResponse]
        );
        $config = [
            'clientId' => 'clientTestID',
            'clientSecret' => 'apiTestSecretKey',
            'httpClientHandler' => $httpClient,
            'contextResolver' => function (RequestInterface $request) {
                return [];
            }
        ];
        $frontegg = new Frontegg($config);

        $webhookBody = new WebHookBody(
            [
                'field 1' => 'value 1',
                'field 2' => 'value 2',
                'field 3' => 'value 3',
            ]
        );

        $channelsConfiguration = new ChannelsConfig();
        $channelsConfiguration->setWebhook($webhookBody);

        $triggerOptions = new TriggerOptions(
            'event-key',
            new DefaultProperties(
                'Default notification title',
                'Default notification description!'
            ),
            $channelsConfiguration,
            'THE-TENANT-ID'
        );

        // Act
        $isSuccess = $frontegg->triggerEvent($triggerOptions);

        // Assert
        $this->assertTrue($isSuccess);
        $this->assertNull($frontegg->getEventsClient()->getApiError());
    }
}
