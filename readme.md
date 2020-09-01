# Frontegg PHP SDK

![alt text](https://fronteggstuff.blob.core.windows.net/frongegg-logos/logo-transparent.png)

Frontegg is a web platform where SaaS companies can set up their fully managed, scalable and brand aware - SaaS features and integrate them into their SaaS portals in up to 5 lines of code.


## Installation

Use the package manager [Composer](https://getcomposer.org/) to install Frontegg SDK for PHP.

```bash
composer require frontegg/php-sdk
```

## Usage

Frontegg offers multiple components for integration with the Frontegg's scalable back-end and front-end libraries.

### Simple Examples

Minimal configuration:

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use Psr\Http\Message\RequestInterface;

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'contextResolver' => function(RequestInterface $request) {
        return [
            'tenantId' => 'THE-TENANT-ID',
            'userId' => 'test-user-id',
            'permissions' => [],
        ];
    },
];

$frontegg = new Frontegg($config);
$frontegg->init();
````

Advanced configuration:

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Psr\Http\Message\RequestInterface;

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'contextResolver' => function(RequestInterface $request) {
        return [
            'tenantId' => 'THE-TENANT-ID',
            'userId' => 'test-user-id',
            'permissions' => [],
        ];
    },
    'disableCors' => false, // You can enable/disable CORS headers for Middleware Proxy.
    'httpClientHandler' => new FronteggCurlHttpClient(), // You can provide custom HTTP client.
];

$frontegg = new Frontegg($config);
$frontegg->init();
````

### Configuration

| Option Name       | Type   | Default Value | Note |
|-------------------|:---:|:---:|---|
| **clientId**      | string | None | Client Id. Required |
| **clientSecret**  | string | None | API Key. Required |
| **contextResolver**   | callable | None | Callback to provide context info. Required |
| apiBaseUrl        | string | https://api.frontegg.com | Base API URL |
| apiUrls           | array | [] | List of URLs of the API services |
| disableCors       | bool | false | Disabling CORS headers for Middleware Proxy |
| httpClientHandler | special interface* | Curl client** | Custom HTTP client |
| *apiVersion*      | string | 'v1' | **Not used yet.** API version |

*special interface - `Frontegg\HttpClient\FronteggHttpClientInterface`,

**Curl client - `Frontegg\HttpClient\FronteggCurlHttpClient`

### Audits

Let your customers record the events, activities and changes made to their tenant.

Frontegg’s Managed Audit Logs feature allows a SaaS company to embed an end-to-end working feature in just several lines of code.

#### Sending audits

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Psr\Http\Message\RequestInterface;

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'contextResolver' => function(RequestInterface $request) {
        return [
            'tenantId' => 'THE-TENANT-ID',
            'userId' => 'test-user-id',
            'permissions' => [],
        ];
    },
    'disableCors' => false, // You can enable/disable CORS headers for Middleware Proxy.
    'httpClientHandler' => new FronteggCurlHttpClient(), // You can provide custom HTTP client.
    'apiVersion' => 'v1', // Not used yet. Coming soon.
];

$frontegg = new Frontegg($config);
$auditLog = $frontegg->sendAudit('THE-TENANT-ID', [
    'user' => 'testuser@t.com',
    'resource' => 'Portal',
    'action' => 'Login',
    'severity' => 'Info',
    'ip' => '123.1.2.3',
]);
````

#### Fetching audits

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use Frontegg\HttpClient\FronteggCurlHttpClient;
use Psr\Http\Message\RequestInterface;

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'contextResolver' => function(RequestInterface $request) {
        return [
            'tenantId' => 'THE-TENANT-ID',
            'userId' => 'test-user-id',
            'permissions' => [],
        ];
    },
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'httpClientHandler' => new FronteggCurlHttpClient(), // You can provide custom HTTP client.
];

$frontegg = new Frontegg($config);
$auditsLog = $frontegg->getAudits(
    'THE-TENANT-ID',
    'Text to filter',
    0, // Offset
    10, // Count
    'action', // Field to sort by
    'ASC' // Sort direction ('ASC' or 'DESC')
    // ... Additional filters
);
````

### Events

Events triggering is easy and maximum configurable for different notification channels.

````php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Events\Config\ChannelsConfig;
use Frontegg\Events\Config\DefaultProperties;
use Frontegg\Events\Config\TriggerOptions;
use Frontegg\Events\Channel\WebHookBody;
use Frontegg\Frontegg;
use Psr\Http\Message\RequestInterface;

$clientId = 'YOUR_CLIENT_ID';
$apikey = 'YOUR_API_KEY';
$config = [
    'clientId' => $clientId,
    'clientSecret' => $apikey,
    'contextResolver' => function(RequestInterface $request) {
        return [
            'tenantId' => 'THE-TENANT-ID',
            'userId' => 'test-user-id',
            'permissions' => [],
        ];
    },
];

$frontegg = new Frontegg($config);

$triggerOptions = new TriggerOptions(
    'eventKeyForTest',
    new DefaultProperties(
        'Default title',
        'Default description',
        [
            'name' => 'Policy 4',
            'id' => '11223456783245234',
        ]
    ),
    new ChannelsConfig(
        new WebHookBody([
            'title' => 'Test title!',
        ])
    ),
    'YOUR_TENANT_ID'
);
$response = $frontegg->triggerEvent($triggerOptions);
````

### Middleware (Proxy)

The Frontegg Proxy forwards requests to the Frontegg API and pass back responses.

There is no Middleware (filters mechanism for HTTP request) in raw PHP, but in some frameworks it is. For example, in Laravel.

Here you can see example for raw PHP. You can easily adapt it for your framework with Middleware (for Laravel see https://github.com/frontegg/samples/tree/master/frontegg-laravel-starter). 

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Setup routing rule for "/frontegg" URIs.
 * Can be a part of middleware f.e. in Laravel.
 */
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/frontegg') === 0) {
    $request = new Request('POST', $_SERVER['REQUEST_URI']);

    $response = handleFronteggUri($request);
}

// ...

function handleFronteggUri(RequestInterface $request)
{
    $clientId = 'YOUR_CLIENT_ID';
    $apikey = 'YOUR_API_KEY';
    $config = [
        'clientId' => $clientId,
        'clientSecret' => $apikey,
        'apiBaseUrl' => 'https://dev-api.frontegg.com/',
        'contextResolver' => function(RequestInterface $request) {
            return [
                'tenantId' => 'THE-TENANT-ID',
                'userId' => 'test-user-id',
                'permissions' => [],
            ];
        },
        'disableCors' => false,
    ];

    $frontegg = new Frontegg($config);
    $response = $frontegg->forward($request);

    return $response->getBody(); 
}
````