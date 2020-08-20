<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Config\Config;
use Frontegg\Event\Type\ChannelsConfig;
use Frontegg\Event\Type\DefaultProperties;
use Frontegg\Event\Type\TriggerOptions;
use Frontegg\Event\Type\WebHookBody;
use Frontegg\Frontegg;

$clientId = '6da27373-1572-444f-b3c5-ef702ce65123';
$apikey = '0cf38799-1dae-488f-8dc8-a09b4c397ad5';
$config = [
    'clientId' => $clientId,
    'clientSecret' => $apikey,
    'apiBaseUrl' => 'https://dev-api.frontegg.com/',
    'apiUrls' => [
        Config::SERVICE_AUTHENTICATION => '/auth/vendor',
        Config::SERVICE_EVENTS => Config::SERVICE_EVENTS_DEFAULT_URL,
    ],
];
$tenantId = 'tacajob400@icanav.net';

$frontegg = new Frontegg($config);


$triggerOptions = new TriggerOptions(
    'event-key-for-test',
    new DefaultProperties(
        'Default title',
        'Default description'
    ),
    new ChannelsConfig(
        new WebHookBody([
            'title' => 'Test title!',
        ])
    ),
    'THE-TENANT-ID'
);
$response = $frontegg->triggerEvent($triggerOptions);

var_dump('--- Config:');
var_dump($frontegg->getConfig());

var_dump('-------- Event triggered response:');
var_dump($response);

if ($frontegg->getAuthenticator()
    && $frontegg->getAuthenticator()->getAccessToken()
    && $frontegg->getAuthenticator()->getAccessToken()->getValue() !== null
) {
    print "\n\nSUCCESS";
} else {
    print "\n\nFAILURE";
}