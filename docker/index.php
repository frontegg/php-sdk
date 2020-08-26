<?php

require_once '../vendor/autoload.php';

use Frontegg\Frontegg;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/* Configure Frontegg SDK. */
/**
 * Your Client ID. You can setup env variable or change default value here.
 */
$clientId = getenv('FRONTEGG_CLIENT_ID')
    ? getenv('FRONTEGG_CLIENT_ID')
    : '6da27373-1572-444f-b3c5-ef702ce65123';
/**
 * Your Secret API Key. You can setup env variable or change default value here.
 */
$apikey = getenv('FRONTEGG_CLIENT_SECRET_KEY')
    ? getenv('FRONTEGG_CLIENT_SECRET_KEY')
    : '0cf38799-1dae-488f-8dc8-a09b4c397ad5';
$config = [
    'clientId' => $clientId,
    'clientSecret' => $apikey,
    'apiBaseUrl' => 'https://dev-api.frontegg.com/',
    'contextResolver' => function (RequestInterface $request) {
        return [
            'tenantId' => 'tacajob400@icanav.net',
            'userId' => 'TEST-USER-ID',
            'permissions' => [],
        ];
    },
    'disableCors' => false,
];

$frontegg = new Frontegg($config);

/**
 * Setup routing rule for "/frontegg" URIs.
 * Can be a part of middleware f.e. in Laravel.
 */
if (isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '/frontegg') === 0
) {
    $request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

    $response = $frontegg->forward($request);

    $decodedBody = json_decode($response->getBody(), true) ??
        $response->getBody();
    print "<pre>";
    var_dump(
        $response->getHttpResponseCode(),
        $decodedBody,
        $response->getHeaders()
    );
    print "</pre>";
    exit;
}

print "<pre>";
printf('"%s" URL does not match to "%s"', $_SERVER['REQUEST_URI'], '/frontegg');
print "</pre>";
