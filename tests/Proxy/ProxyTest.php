<?php

namespace Frontegg\Tests\Proxy;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\FronteggHttpClient\FronteggAdapter;
use Frontegg\Proxy\Proxy;
use Frontegg\Tests\Helper\ProxyTestCaseHelper;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class ProxyTest extends ProxyTestCaseHelper
{
    /**
     * @return void
     */
    public function testProxyCanBeCreated(): void
    {
        // Arrange
        $httpClient = $this->createFronteggCurlHttpClientStub();
        $contextResolver = function (RequestInterface $request) {
            return [
                'tenantId' => 'THE-TENANT-ID',
                'userId' => 'test-user-id',
                'permissions' => [],
            ];
        };

        // Act
        $object = $this->createFronteggProxy($httpClient, $contextResolver);

        // Assert
        $this->assertInstanceOf(Proxy::class, $object);
        $this->assertInstanceOf(
            Authenticator::class,
            $object->getAuthenticator()
        );
        $this->assertInstanceOf(
            FronteggHttpClientInterface::class,
            $object->getAuthenticator()->getClient()
        );
    }

    /**
     * @return void
     */
    public function testProxyShouldForwardGetRequestToFronteggApi(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $apiResponse = new ApiRawResponse(
            ['Content-type' => 'application/json'],
            '{
                "data":[
                    {
                        "title":"Default title",
                        "severity":"Info",
                        "tenantId":"tacajob400@icanav.net",
                        "vendorId":"6da27373-1572-444f-b3c5-ef702ce65123",
                        "createdAt":"2020-08-22 06:47:25.025",
                        "description":"Default description",
                        "frontegg_id":"6eacf416-67e2-4760-85d7-9ab90a18a945"
                    }
                ]
            }',
            200
        );

        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$authResponse, $apiResponse]
        );
        $contextResolver = function (RequestInterface $request) {
            return [
                'tenantId' => 'THE-TENANT-ID',
                'userId' => 'test-user-id',
                'permissions' => [],
            ];
        };

        $object = $this->createFronteggProxy($httpClient, $contextResolver);
        $request = new Request('GET', '/get-request');

        // Act
        /** @var ApiRawResponse $response */
        $response = $object->forwardTo(
            $request,
            'https://dev-api.frontegg.com/'
        );

        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertJson($response->getBody());
    }

    /**
     * @return void
     */
    public function testProxyShouldForwardPostRequestWithUrlEncodedDataToFronteggApi(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $apiResponse = new ApiRawResponse(
            ['Content-type' => 'application/json'],
            '{"status": "success"}',
            200
        );

        $httpClient = $this->createFronteggCurlHttpClientStub(
            [$authResponse, $apiResponse]
        );
        $contextResolver = function (RequestInterface $request) {
            return [
                'tenantId' => 'THE-TENANT-ID',
                'userId' => 'test-user-id',
                'permissions' => [],
            ];
        };

        $object = $this->createFronteggProxy($httpClient, $contextResolver);
        $request = new Request('POST', '/request-with-post-data');

        // Act
        /** @var ApiRawResponse $response */
        $response = $object->forwardTo(
            $request,
            'https://dev-api.frontegg.com/'
        );

        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertJson($response->getBody());
    }

    /**
     * @return void
     */
    public function testProxyShouldForwardRequestToFronteggApiAndReturnError(): void
    {
        // Arrange
        $authResponse = $this->createAuthHttpApiRawResponse();
        $apiResponse = new ApiRawResponse(
            ['Content-type' => 'application/json'],
            '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Error</title>
</head>
<body>
<pre>Cannot GET /</pre>
</body>
</html>',
            404
        );

        $httpClient = $this->createFronteggCurlHttpClientStub(
            [
                $authResponse,
                $apiResponse,
                clone($apiResponse),
                clone($apiResponse),
                clone($apiResponse),
            ]
        );
        $contextResolver = function (RequestInterface $request) {
            return [
                'tenantId' => 'THE-TENANT-ID',
                'userId' => 'test-user-id',
                'permissions' => [],
            ];
        };

        $object = $this->createFronteggProxy($httpClient, $contextResolver);
        $request = new Request('GET', '/wrong-url');

        // Act
        /** @var ApiRawResponse $response */
        $response = $object->forwardTo(
            $request,
            'https://dev-api.frontegg.com/'
        );

        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertEquals(500, $response->getHttpResponseCode());
        $this->assertEquals('Frontegg request failed', $response->getBody());
    }
}
