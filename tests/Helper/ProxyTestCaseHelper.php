<?php

namespace Frontegg\Tests\Helper;

use Frontegg\HttpClient\FronteggHttpClientInterface;
use Frontegg\Proxy\Adapter\FronteggHttpClient\FronteggAdapter;
use Frontegg\Proxy\Proxy;

class ProxyTestCaseHelper extends AuthenticatorTestCaseHelper
{
    public function createFronteggProxy(
        FronteggHttpClientInterface $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = []
    ): Proxy {
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls
        );
        $clientAdapter = new FronteggAdapter($client);

        return new Proxy($authenticator, $clientAdapter);
    }
}