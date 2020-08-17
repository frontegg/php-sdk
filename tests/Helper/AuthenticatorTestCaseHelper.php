<?php

namespace Frontegg\Tests\Helper;

use Frontegg\Authenticator\Authenticator;
use Frontegg\Config\Config;
use Frontegg\Http\ApiRawResponse;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class AuthenticatorTestCaseHelper extends TestCase
{
    /**
     * @param FronteggCurlHttpClient $client
     * @param string                 $clientId
     * @param string                 $clientSecret
     * @param string                 $baseUrl
     * @param array                  $urls
     *
     * @return Authenticator
     */
    protected function createFronteggAuthenticator(
        FronteggCurlHttpClient $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = []
    ): Authenticator {
        $fronteggConfig = new Config($clientId, $clientSecret, $baseUrl, $urls);

        return new Authenticator($fronteggConfig, $client);
    }

    /**
     * @param ApiRawResponse[] $authResponses
     *
     * @return Stub|FronteggCurlHttpClient
     */
    protected function createFronteggCurlHttpClientStub(
        array $authResponses = []
    ): Stub {
        $client = $this->createStub(FronteggCurlHttpClient::class);
        $client->method('send')
            ->willReturnOnConsecutiveCalls(
                ...$authResponses
            );

        return $client;
    }

    /**
     * @param string $accessToken
     * @param int    $expiresIn
     * @param int    $httpStatusCode
     *
     * @return ApiRawResponse
     */
    protected function createAuthHttpApiRawResponse(
        string $accessToken = 'YOUR-JWT-TOKEN',
        int $expiresIn = 1800,
        int $httpStatusCode = 200
    ): ApiRawResponse {
        return new ApiRawResponse(
            [],
            sprintf(
                '{
                    "token": "%s",
                    "expiresIn": %d
                }',
                $accessToken,
                $expiresIn
            ),
            $httpStatusCode
        );
    }

    /**
     * @param int    $statusCode
     * @param string $error
     * @param string $message
     * @param int    $httpStatusCode
     *
     * @return ApiRawResponse
     */
    protected function createAuthFailureHttpApiRawResponse(
        int $statusCode = 401,
        string $error = 'Unauthorized',
        string $message = 'Could not verify vendor',
        int $httpStatusCode = 401
    ): ApiRawResponse {
        return new ApiRawResponse(
            [],
            sprintf(
                '{
                    "statusCode": %s,
                    "error": "%s",
                    "message": "%s"
                }',
                $statusCode,
                $error,
                $message
            ),
            $httpStatusCode
        );
    }
}