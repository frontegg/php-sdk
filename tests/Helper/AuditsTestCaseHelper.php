<?php

namespace Frontegg\Tests\Helper;

use Frontegg\Audit\AuditsClient;
use Frontegg\HttpClient\FronteggHttpClientInterface;

abstract class AuditsTestCaseHelper extends AuthenticatorTestCaseHelper
{
    /**
     * @param FronteggHttpClientInterface $client
     * @param string                      $clientId
     * @param string                      $clientSecret
     * @param string                      $baseUrl
     * @param array                       $urls
     *
     * @return AuditsClient
     */
    protected function createFronteggAuditsClient(
        FronteggHttpClientInterface $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = []
    ): AuditsClient {
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls
        );

        return new AuditsClient($authenticator);
    }
}
