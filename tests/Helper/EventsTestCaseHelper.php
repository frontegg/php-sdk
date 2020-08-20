<?php

namespace Frontegg\Tests\Helper;

use Frontegg\Event\EventsClient;
use Frontegg\HttpClient\FronteggCurlHttpClient;

class EventsTestCaseHelper extends AuthenticatorTestCaseHelper
{
    /**
     * @param FronteggCurlHttpClient $client
     * @param string                 $clientId
     * @param string                 $clientSecret
     * @param string                 $baseUrl
     * @param array                  $urls
     *
     * @return EventsClient
     */
    public function createFronteggEventsClient(
        FronteggCurlHttpClient $client,
        string $clientId = 'clientTestID',
        string $clientSecret = 'apiTestSecretKey',
        string $baseUrl = 'http://test',
        array $urls = []
    ): EventsClient {
        $authenticator = $this->createFronteggAuthenticator(
            $client,
            $clientId,
            $clientSecret,
            $baseUrl,
            $urls
        );

        return new EventsClient($authenticator);
    }
}