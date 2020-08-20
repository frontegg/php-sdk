<?php

namespace Frontegg\Tests\Audit;

use DateTime;
use Frontegg\Exception\AuthenticationException;
use Frontegg\Http\ApiRawResponse;
use Frontegg\Tests\Helper\AuditsTestCaseHelper;

class AuditsClientTest extends AuditsTestCaseHelper
{
    /**
     * @return void
     */
    public function testAuditsClientHasAccessToken(): void
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
        $auditsClient = $this->createFronteggAuditsClient(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse, $auditsResponse]
            )
        );

        // Act
        $auditsClient->getAudits('THE-TENANT-ID');

        // Assert
        $this->assertNotNull(
            $auditsClient->getAuthenticator()->getAccessToken()
        );
        $this->assertGreaterThan(
            (new DateTime())->getTimestamp(),
            $auditsClient->getAuthenticator()
                ->getAccessToken()
                ->getExpiresAt()
                ->getTimestamp()
        );
    }

    /**
     * @return void
     */
    public function testAuditsClientGetsAuditsLog(): void
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
        $auditsClient = $this->createFronteggAuditsClient(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse, $auditsResponse]
            )
        );

        // Act
        $auditLogs = $auditsClient->getAudits('THE-TENANT-ID');

        // Assert
        $this->assertNotEmpty($auditLogs['data']);
        $this->assertGreaterThanOrEqual(2, count($auditLogs['data']));
        $this->assertNotEmpty($auditLogs['total']);
        $this->assertContains(['log1'], $auditLogs['data']);
        $this->assertContains(['log 2'], $auditLogs['data']);
    }

    /**
     * @return void
     */
    public function testAuditsClientGetsAuditsLogWithError(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $auditsResponse = new ApiRawResponse(
            [],
            'Authentication required',
            401
        );
        $auditsClient = $this->createFronteggAuditsClient(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse, $auditsResponse]
            )
        );

        // Act
        $this->expectException(AuthenticationException::class);
        $auditsClient->getAudits('THE-TENANT-ID');
    }

    /**
     * @return void
     */
    public function testAuditsClientCanCreateNewAuditLog(): void
    {
        // Arrange
        $auditsLogData = [
            'user' => 'testuser@t.com',
            'resource' => 'Portal',
            'action' => 'Login',
            'severity' => 'Info',
            'ip' => '123.1.2.3',
        ];
        $authResponse = $this->createAuthHttpApiRawResponse();
        $auditsResponse = new ApiRawResponse(
            [],
            json_encode($auditsLogData),
            200
        );

        $auditsClient = $this->createFronteggAuditsClient(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse, $auditsResponse]
            )
        );

        // Act
        $sentAuditsLog = $auditsClient->sendAudit('TENANT-ID', $auditsLogData);

        // Assert
        $this->assertSame($auditsLogData, $sentAuditsLog);
    }

    /**
     * @return void
     */
    public function testAuditsClientGetsErrorOnCreatingNewAuditLog(): void
    {
        // Arrange
        $auditsLogData = [
            'user' => 'testuser@t.com',
            'resource' => 'Portal',
            'action' => 'Login',
            'severity' => 'Info',
            'ip' => '123.1.2.3',
        ];
        $authResponse = $this->createAuthHttpApiRawResponse();
        $auditsResponse = new ApiRawResponse(
            [],
            'Authentication required',
            401
        );

        $auditsClient = $this->createFronteggAuditsClient(
            $this->createFronteggCurlHttpClientStub(
                [$authResponse, $auditsResponse]
            )
        );

        // Act
        $this->expectException(AuthenticationException::class);
        $auditsClient->sendAudit('THE-TENANT-ID', $auditsLogData);
    }

    // @TODO: Add one test for additional filter parameters for method getAudits().
}
