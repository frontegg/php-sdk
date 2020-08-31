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
    : 'THE-CLIENT-ID';
/**
 * Your Secret API Key. You can setup env variable or change default value here.
 */
$apiKey = getenv('FRONTEGG_CLIENT_SECRET_KEY')
    ? getenv('FRONTEGG_CLIENT_SECRET_KEY')
    : 'THE-API-KEY';
/**
 * Your tenant ID. You can setup env variable or change default value here.
 */
$tenantId = getenv('FRONTEGG_TENANT_ID')
    ? getenv('FRONTEGG_TENANT_ID')
    : 'THE-TENANT-ID';
$config = [
    'clientId' => $clientId,
    'clientSecret' => $apiKey,
    'apiBaseUrl' => 'https://dev-api.frontegg.com/',
    'contextResolver' => function (RequestInterface $request) use ($tenantId) {
        return [
            'tenantId' => $tenantId,
            'userId' => 'TEST-USER-ID',
            'permissions' => [],
        ];
    },
    'disableCors' => true,
];


/**
 * Initialize the main Frontegg SDK component.
 */
$frontegg = new Frontegg($config);

/**
 * Setup routing rule for "/frontegg" URIs.
 * Can be a part of middleware f.e. in Laravel.
 */
if (isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '/frontegg') === 0
) {
    $request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

    try {
        $response = $frontegg->forward($request);
    } catch (Exception $e) {
        sendResponse(500, [], sprintf('Error happened: %s', $e->getMessage()));
    }

    sendResponse($response->getHttpResponseCode(), $response->getHeaders(), $response->getBody());
}

/**
 * Setup routing rule for POST "/audit" URIs.
 * Can be a part of middleware f.e. in Laravel.
 */
elseif (isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '/audit') === 0
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $payload = file_get_contents('php://input');
    $auditLog = json_decode($payload, true);

    try {
        $responseData = $frontegg->sendAudit($tenantId, $auditLog);
    } catch (Exception $e) {
        sendResponse(500, [], sprintf('Error happened: %s', $e->getMessage()));
    }

    file_put_contents(__DIR__ . '/logs/responses201.txt', json_encode($responseData)  . "\n", FILE_APPEND);
    sendResponse(201, ['Content-Type' => ['application/json']], json_encode($responseData));
}

/**
 * ONLY FOR CORS!
 * Setup routing rule for POST "/audit" URIs.
 * Can be a part of middleware f.e. in Laravel.
 */
elseif (isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '/audit') === 0
    && $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
) {
    sendResponse(200, [], '');
}

/**
 * Default not found error response.
 */
sendResponse(
    404,
    [],
    sprintf('"%s" URL not found', $_SERVER['REQUEST_URI'])
);


// --- Helper functions ---

/**
 * Sends response to the client.
 * Stops script running.
 *
 * @param int    $httpCode
 * @param array  $headers
 * @param string $body
 */
function sendResponse($httpCode = 200, array $headers = [], string $body = ''): void
{
    http_response_code($httpCode);
    /**
     * This is correct handling for CORS.
     */
    cors();
    sendHeaders($headers);
    print $body;

    exit;
}

/**
 * Send HTTP headers if they have not been sent yet.
 *
 * @param array $headers
 *
 * @return void
 */
function sendHeaders(array $headers = ['Content-Type' => ['text/html']]): void
{
    if (headers_sent()) {
        return;
    }

    foreach ($headers as $name => $headerValues) {
        foreach ($headerValues as $value) {
            header(sprintf('%s:%s', $name, $value), false);
        }
    }
}

/**
 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
 *  origin.
 *
 *  In a production environment, you probably want to be more restrictive, but this gives you
 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
 *
 *  - https://developer.mozilla.org/en/HTTP_access_control
 *  - http://www.w3.org/TR/cors/
 *
 */
function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
}
