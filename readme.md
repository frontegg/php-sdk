# Frontegg PHP SDK

![alt text](https://fronteggstuff.blob.core.windows.net/frongegg-logos/logo-transparent.png)

Frontegg is a web platform where SaaS companies can set up their fully managed, scalable and brand aware - SaaS features and integrate them into their SaaS portals in up to 5 lines of code.


## Installation

Use the package manager [Composer](https://getcomposer.org/) to install Frontegg SDK for PHP.

```bash
composer install frontegg/php-sdk
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

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
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

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'apiVersion' => 'v1', // Not used yet. Coming soon.
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
| apiBaseUrl        | string | https://api.frontegg.com | Base API URL |
| apiUrls           | array | | List of URLs of the API services |
| httpClientHandler | special interface* | Curl client** | Custom HTTP client |
| *apiVersion*      | string | 'v1' | **Not used yet.** API version |

*special interface - `Frontegg\HttpClient\FronteggHttpClientInterface`,

**Curl client - `Frontegg\HttpClient\FronteggCurlHttpClient`

### Audits

Let your customers record the events, activities and changes made to their tenant.

Frontegg’s Managed Audit Logs feature allows a SaaS company to embed an end-to-end working feature in just 5 lines of code.

#### Sending audits

````php
<?php

require_once './vendor/autoload.php';
require_once './src/Frontegg/autoload.php';

use Frontegg\Frontegg;
use Frontegg\HttpClient\FronteggCurlHttpClient;

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'apiVersion' => 'v1', // Not used yet. Coming soon.
    'httpClientHandler' => new FronteggCurlHttpClient(), // You can provide custom HTTP client.
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

$config = [
    'clientId' => 'YOUR_CLIENT_ID',
    'clientSecret' => 'YOUR_SECRET_API_KEY',
    'apiBaseUrl' => 'https://api.frontegg.com/',
    'apiUrls' => [
        'authentication' => '/auth/vendor',
        'audits' => '/audits',
    ],
    'apiVersion' => 'v1', // Not used yet. Coming soon.
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

!!!!!TODO:!!!!!