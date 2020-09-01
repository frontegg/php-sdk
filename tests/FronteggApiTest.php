<?php

namespace Frontegg\Tests;

use Frontegg\Authenticator\AccessToken;
use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Events\Config\ChannelsConfig;
use Frontegg\Events\Config\DefaultProperties;
use Frontegg\Events\Config\TriggerOptions;
use Frontegg\Frontegg;
use Frontegg\Http\ApiRawResponse;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class FronteggApiTest
 *
 * @package Frontegg
 *
 * @group   Frontegg
 * @group   FronteggApi
 */
class FronteggApiTest extends TestCase
{
    // Test credentials.
    protected const CLIENT_ID = 'FRONTEGG_TEST_CLIENT_ID';
    protected const API_KEY = 'FRONTEGG_TEST_API_KEY';
    protected const API_BASE_URL = 'FRONTEGG_TEST_API_BASE_URL';

    protected const TENANT_ID = 'FRONTEGG_TEST_TENANT_ID';

    /**
     * Frontegg API client.
     *
     * @var Frontegg
     */
    protected $fronteggClient;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $config = [
            'clientId' => getenv(self::CLIENT_ID),
            'clientSecret' => getenv(self::API_KEY),
            'apiBaseUrl' => getenv(self::API_BASE_URL),
            'contextResolver' => function (RequestInterface $request) {
                return [
                    'tenantId' => getenv(self::TENANT_ID),
                    'userId' => 'test-user-id',
                    'permissions' => [],
                ];
            },
            'disableCors' => false,
        ];
        $this->fronteggClient = new Frontegg($config);
    }

    /**
     * @return void
     */
    public function testAuthenticationIsWorking(): void
    {
        // Act
        $this->fronteggClient->init();

        // Assert
        $this->assertInstanceOf(
            Authenticator::class,
            $this->fronteggClient->getAuthenticator()
        );
        $this->assertInstanceOf(
            AccessToken::class,
            $this->fronteggClient->getAuthenticator()->getAccessToken()
        );
    }

    /**
     * @throws \Frontegg\Exception\AuthenticationException
     * @throws \Frontegg\Exception\FronteggSDKException
     *
     * @return void
     */
    public function testAuditsClientCanCreateAndGetThreeAuditLogs(): void
    {
        // Arrange
        $auditsLogData = [
            [
                'user' => 'testuser2@t.com',
                'resource' => 'Portal',
                'action' => 'Testing',
                'severity' => 'Info',
                'ip' => '123.1.2.33',
            ],
            [
                'user' => 'testuser3@t.com',
                'resource' => 'Portal',
                'action' => 'Testing',
                'severity' => 'Info',
                'ip' => '123.1.2.34',
            ],
            [
                'user' => 'testuser2@t.com',
                'resource' => 'Portal',
                'action' => 'Testing',
                'severity' => 'Info',
                'ip' => '123.1.2.33',
            ],
        ];

        foreach ($auditsLogData as $auditLog) {
            $this->fronteggClient->sendAudit(
                self::TENANT_ID,
                $auditLog
            );
        }

        // Act
        $auditLogs = $this->fronteggClient->getAudits(
            self::TENANT_ID,
            'Testing',
            0,
            3,
            'createdAt',
            'desc'
        );

        // Assert
        $this->assertNotEmpty($auditLogs['data']);
        $this->assertGreaterThanOrEqual(3, count($auditLogs['data']));
        $this->assertNotEmpty($auditLogs['total']);
        foreach ($auditLogs['data'] as $auditLog) {
            $this->assertAuditLogsContainsAuditLog(
                $auditLog,
                $auditsLogData
            );
        }
    }

    /**
     * @throws \Frontegg\Exception\EventTriggerException
     * @throws \Frontegg\Exception\FronteggSDKException
     * @throws \Frontegg\Exception\InvalidParameterException
     * @throws \Frontegg\Exception\InvalidUrlConfigException
     *
     * @return void
     */
    public function testEventsClientCanTriggerEvent(): void
    {
        // Arrange
        $triggerOptions = new TriggerOptions(
            'eventKeyForTest',
            new DefaultProperties(
                'Default title',
                'Default description'
            ),
            new ChannelsConfig(
                new WebHookBody([
                    'title' => 'Test title!',
                ])
            ),
            self::TENANT_ID
        );

        // Act
        $isSuccess = $this->fronteggClient->triggerEvent($triggerOptions);

        // Assert
        $this->assertTrue($isSuccess);
        $this->assertNull($this->fronteggClient->getEventsClient()->getApiError());
    }

    /**
     * @throws \Frontegg\Exception\UnexpectedValueException
     *
     * @return void
     */
    public function testProxyCanForwardPostAuditLogs(): void
    {
        // Arrange
        $auditLogData = [
            'user' => 'testuser@t.com',
            'resource' => 'Portal',
            'action' => 'Login',
            'severity' => 'Info',
            'ip' => '123.1.2.3',
        ];
        $request = new Request(
            'POST',
            Config::PROXY_URL . '/audits',
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            http_build_query($auditLogData)
        );

        // Act
        $response = $this->fronteggClient->forward($request);


        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertContains($response->getHttpResponseCode(), [200, 202]);
        $this->assertNotEmpty($response->getHeaders());
        $this->assertJson($response->getBody());
    }

    /**
     * @throws \Frontegg\Exception\UnexpectedValueException
     *
     * @return void
     */
    public function testProxyCanForwardGetAuditLogs(): void
    {
        // Arrange
        $request = new Request(
            'GET',
            Config::PROXY_URL . '/audits?sortDirection=desc&sortBy=createdAt&filter=&offset=0&count=20'
        );

        // Act
        $response = $this->fronteggClient->forward($request);

        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertJson($response->getBody());
    }

    /**
     * Assert that audit logs collection has the current audit log.
     *
     * @param array  $needle
     * @param array  $haystack
     * @param string $errorMessage
     */
    protected function assertAuditLogsContainsAuditLog(
        array $needle,
        array $haystack,
        string $errorMessage = 'Audit logs "%2$s" should contain "%1$s"'
    ): void {
        $auditLog = [
            'user' => $needle['user'],
            'resource' => $needle['resource'],
            'action' => $needle['action'],
            'severity' => $needle['severity'],
            'ip' => $needle['ip'],
        ];

        $this->assertContains(
            $auditLog,
            $haystack,
            sprintf(
                $errorMessage,
                print_r($auditLog, true),
                print_r($haystack, true)
            )
        );
    }
}
