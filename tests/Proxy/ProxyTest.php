<?php

namespace Frontegg\Tests\Proxy;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\FronteggHttpClient\FronteggAdapter;
use Frontegg\Proxy\Proxy;
use Frontegg\Tests\Helper\ProxyTestCaseHelper;
use GuzzleHttp\Psr7\Request;

class ProxyTest extends ProxyTestCaseHelper
{
    /**
     * @return void
     */
    public function testProxyCanBeCreated(): void
    {
        // Arrange
        $httpClient = $this->createFronteggCurlHttpClientStub();

        // Act
        $object = $this->createFronteggProxy($httpClient);

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
    public function testProxyShouldForwardRequestToFronteggApi(): void
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
        $object = $this->createFronteggProxy($httpClient);
        $request = new Request('GET', 'https://google.com');

        // Act
        /** @var ApiRawResponse $response */
        $response = $object->forward($request)
            ->to('https://dev-api.frontegg.com/frontegg');
        var_dump($response);

        // Assert
        $this->assertInstanceOf(ApiRawResponse::class, $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertJson($response->getBody());
    }
}
