<?php

namespace Frontegg\Tests\Helper;

use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\FronteggHttpClient\FronteggAdapter;
use Frontegg\Proxy\Proxy;

class ProxyTestCaseHelper extends AuthenticatorTestCaseHelper
{
    /**
     * @param FronteggHttpClientInterface $client
     * @param callable                    $contextResolver
     * @param string                      $clientId
     * @param string                      $clientSecret
     * @param string                      $baseUrl
     * @param array                       $urls
     *
     * @return Proxy
     */
    public function createFronteggProxy(
        FronteggHttpClientInterface $client,
        callable $contextResolver,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = [],
        bool $disbaleCors = true
    ): Proxy {
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls,
            $disbaleCors,
            $contextResolver
        );
        $clientAdapter = new FronteggAdapter($client);

        return new Proxy($authenticator, $clientAdapter, $contextResolver);
    }
}
